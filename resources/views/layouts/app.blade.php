<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Cashflow Manager') }}</title>
    <link rel="icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">

    {{-- Styles --}}
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @livewireStyles
    <link rel="stylesheet" href="/assets/vendor/bootstrap-5.3.8-dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="stylesheet" type="text/css" href="https://unpkg.com/trix@2.0.8/dist/trix.css">
</head>

<body class="bg-light">
    <div class="container-fluid">
        @hasSection('content')
            @yield('content')
        @else
            {{ $slot ?? '' }}
        @endif
    </div>

    {{-- Scripts --}}
    <script type="text/javascript" src="https://unpkg.com/trix@2.0.8/dist/trix.umd.min.js"></script>
    <script src="/assets/vendor/bootstrap-5.3.8-dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    @livewireScripts

    @stack('scripts')
    
    <script>
        document.addEventListener("livewire:initialized", () => {
            
            // Listener untuk menutup modal
            Livewire.on("closeModal", (data) => {
                const modalEl = document.getElementById(data.id);
                if (modalEl) {
                    const modal = bootstrap.Modal.getInstance(modalEl);
                    if (modal) {
                        modal.hide();
                        // Bersihkan backdrop modal jika masih ada
                        const backdrop = document.querySelector('.modal-backdrop');
                        if (backdrop) {
                            backdrop.remove();
                        }
                        // Hapus class modal-open dari body
                        document.body.classList.remove('modal-open');
                        document.body.style.overflow = '';
                        document.body.style.paddingRight = '';
                    }
                }
            });

            // Listener untuk membuka modal
            Livewire.on("showModal", (data) => {
                const modalEl = document.getElementById(data.id);
                if (modalEl) {
                    const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
                    if (modal) {
                        // Bersihkan backdrop lama jika ada
                        const backdrop = document.querySelector('.modal-backdrop');
                        if (backdrop) {
                            backdrop.remove();
                        }
                        document.body.classList.remove('modal-open');
                        modal.show();
                    }
                }
            });

            // [NEW] Listener untuk SweetAlert (Kebutuhan SweetAlert)
            Livewire.on('showSweetAlert', (data) => {
                Swal.fire({
                    icon: data.icon,
                    title: data.title,
                    text: data.text,
                    timer: 3000,
                    timerProgressBar: true,
                    showConfirmButton: false
                });
            });

            // [NEW] Listener untuk Trix Editor (Kebutuhan Trix Editor)
            Livewire.on('setTrixContent', (data) => {
                const trixEditor = document.getElementById(data.id);
                if (trixEditor) {
                    trixEditor.editor.loadHTML(data.content);
                }
            });

        });
    </script>
    @stack('scripts')
</body>

</html>