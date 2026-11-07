<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Otomasi Notifikasi - UNIP</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <strong>Berhasil!</strong> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <strong>Gagal!</strong> {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white text-center py-3">
                    <h5 class="mb-0">Gateway Notifikasi Email (Laravel to n8n)</h5>
                </div>
                <div class="card-body p-4">
                    <!-- Diubah mengarah ke rute password.email yang sesuai dengan route:list -->
                    <form action="{{ route('password.email') }}" method="POST">
                        @csrf

                        <!-- Hidden input agar sistem otomatis mengirim metode email -->
                        <input type="hidden" name="metode_kirim" value="email">

                        <div class="mb-3">
                            <label for="tujuan" class="form-label fw-bold">Alamat Email Tujuan</label>
                            <input type="email" class="form-control" id="tujuan" name="tujuan" placeholder="Contoh: mahasiswa@unip.ac.id" required>
                            <div class="form-text text-muted">Pastikan format alamat email valid.</div>
                        </div>

                        <div class="mb-3">
                            <label for="message" class="form-label fw-bold">Isi Pesan / OTP</label>
                            <textarea class="form-control" id="message" name="message" rows="4" placeholder="Tulis pesan atau kode OTP yang ingin dikirim..." required></textarea>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-lg">Kirim Email via n8n Engine</button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>