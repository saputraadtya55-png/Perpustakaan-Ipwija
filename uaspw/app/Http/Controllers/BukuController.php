<?php

namespace App\Http\Controllers;

use App\Models\Buku;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

class BukuController extends Controller
{
    /**
     * Tampilkan daftar buku di dashboard (dengan Redis Caching)
     */
    public function index(Request $request)
    {
        $search = $request->input('search');

        try {
            // Cek jika ada pencarian, gunakan Elasticsearch
            if (!empty($search)) {
                $buku = $this->searchElasticsearch($search);
            } else {
                // Gunakan Redis Caching dengan fallback ke MySQL
                $buku = Cache::remember('daftar_buku_all', 60, function () {
                    return Buku::all();
                });
            }
        } catch (Exception $e) {
            Log::warning('Redis / Elasticsearch bermasalah, beralih ke MySQL: ' . $e->getMessage());
            $buku = Buku::when($search, function ($query, $search) {
                return $query->where('judul_buku', 'like', "%{$search}%");
            })->get();
        }

        return view('dashboard', compact('buku'));
    }

    /**
     * Proses simpan buku baru + Kirim Webhook ke N8N (Poin f)
     */
    public function store(Request $request)
    {
        // Validasi Form & CSRF
        $validated = $request->validate([
            'judul_buku'   => 'required|string|max:150',
            'pengarang'    => 'required|string|max:100',
            'tahun_terbit' => 'required|integer|digits:4',
        ]);

        // Simpan ke database MySQL
        $buku = Buku::create($validated);

        // Hapus cache Redis agar data ter-update
        try {
            Cache::forget('daftar_buku_all');
        } catch (Exception $e) {
            Log::warning('Gagal menghapus cache Redis: ' . $e->getMessage());
        }

        // Sinkronisasi ke Elasticsearch
        $this->indexToElasticsearch($buku);

        // Poin f: Kirim Webhook ke N8N menggunakan environment variable production yang benar
        try {
            $n8nWebhookUrl = env('N8N_ADD_BOOK_WEBHOOK_URL');

            Http::timeout(3)->post($n8nWebhookUrl, [
                'email_admin'  => 'saputra.adtya55@gmail.com',
                'subject'      => '202401110033-Praditya Wahyu Saputra-Informatika', 
                'judul_buku'   => $buku->judul_buku,
                'pengarang'    => $buku->pengarang,
                'tahun_terbit' => $buku->tahun_terbit,
                'pesan'        => 'Buku baru telah berhasil ditambahkan ke sistem perpustakaan.'
            ]);
        } catch (Exception $e) {
            Log::warning('Gagal mengirim webhook ke N8N: ' . $e->getMessage());
        }

        return redirect()->route('dashboard')->with('success', 'Buku berhasil ditambahkan dan email notifikasi dikirim ke admin!');
    }

    /**
     * Proses update data buku + Kirim Webhook ke N8N
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'judul_buku'   => 'required|string|max:150',
            'pengarang'    => 'required|string|max:100',
            'tahun_terbit' => 'required|integer|digits:4',
        ]);

        $buku = Buku::findOrFail($id);
        $buku->update($validated);

        // Hapus cache Redis
        try {
            Cache::forget('daftar_buku_all');
        } catch (Exception $e) {
            Log::warning('Gagal menghapus cache Redis: ' . $e->getMessage());
        }

        // Update di Elasticsearch
        $this->indexToElasticsearch($buku);

        // Kirim Webhook ke N8N saat Update Data Buku
        try {
            $n8nWebhookUrl = env('N8N_ADD_BOOK_WEBHOOK_URL');

            Http::timeout(3)->post($n8nWebhookUrl, [
                'email_admin'  => 'saputra.adtya55@gmail.com',
                'subject'      => '202401110033-Praditya Wahyu Saputra-Informatika', 
                'judul_buku'   => $buku->judul_buku,
                'pengarang'    => $buku->pengarang,
                'tahun_terbit' => $buku->tahun_terbit,
                'pesan'        => 'Data buku telah berhasil diperbarui di sistem perpustakaan.'
            ]);
        } catch (Exception $e) {
            Log::warning('Gagal mengirim webhook update ke N8N: ' . $e->getMessage());
        }

        return redirect()->route('dashboard')->with('success', 'Buku berhasil diperbarui dan email notifikasi dikirim!');
    }

    /**
     * Proses hapus buku dari MySQL, Redis, dan Elasticsearch
     */
    public function destroy($id)
    {
        $buku = Buku::findOrFail($id);
        
        // Hapus dari Elasticsearch
        try {
            $esHost = env('ELASTICSEARCH_HOST', 'http://host.docker.internal:9200');
            Http::timeout(2)->delete("{$esHost}/buku_index/_doc/{$id}");
        } catch (Exception $e) {
            Log::warning('Gagal menghapus data dari Elasticsearch: ' . $e->getMessage());
        }

        // Hapus dari MySQL
        $buku->delete();

        // Hapus cache Redis
        try {
            Cache::forget('daftar_buku_all');
        } catch (Exception $e) {
            Log::warning('Gagal menghapus cache Redis: ' . $e->getMessage());
        }

        return redirect()->route('dashboard')->with('success', 'Buku berhasil dihapus dari sistem!');
    }

    /**
     * Helper: Pencarian menggunakan Elasticsearch
     */
    private function searchElasticsearch($query)
    {
        $esHost = env('ELASTICSEARCH_HOST', 'http://host.docker.internal:9200');

        $response = Http::timeout(2)->post("{$esHost}/buku_index/_search", [
            'query' => [
                'match' => [
                    'judul_buku' => $query
                ]
            ]
        ]);

        if ($response->successful()) {
            $hits = $response->json()['hits']['hits'] ?? [];
            $ids = collect($hits)->pluck('_id');
            
            return Buku::whereIn('id', $ids)->get();
        }

        return Buku::where('judul_buku', 'like', "%{$query}%")->get();
    }

    /**
     * Helper: Sinkronisasi data buku ke Elasticsearch
     */
    private function indexToElasticsearch($buku)
    {
        try {
            $esHost = env('ELASTICSEARCH_HOST', 'http://host.docker.internal:9200');

            Http::timeout(2)->put("{$esHost}/buku_index/_doc/{$buku->id}", [
                'id'           => $buku->id,
                'judul_buku'   => $buku->judul_buku,
                'pengarang'    => $buku->pengarang,
                'tahun_terbit' => $buku->tahun_terbit,
            ]);
        } catch (Exception $e) {
            Log::warning('Gagal melakukan indexing ke Elasticsearch: ' . $e->getMessage());
        }
    }
}