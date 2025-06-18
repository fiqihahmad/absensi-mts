<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="Sistem Absensi MTs Darul Ishlah">
    <meta name="keywords" content="mts darul ishlah">

    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link rel="shortcut icon" href="{{ asset('assets/static/img/icons/icon-mts.png') }}" />

    <link rel="canonical" href="https://demo-basic.adminkit.io/" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-SgOJa3DmI69IUzQ2PVdRZhwQ+dy64/BUtbMJw1MZ8t5HZApcHrRKUc4W0kG879m7" crossorigin="anonymous">

    <title>@yield('title')</title>

    <link href="{{ asset('assets/static/css/app.css') }}" rel="stylesheet">
    
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600&display=swap" rel="stylesheet">
    
    <link href="https://cdn.datatables.net/v/bs5/jq-3.7.0/dt-2.2.2/r-3.0.4/datatables.min.css" rel="stylesheet" integrity="sha384-XtYbVlTRVS4Pb9ZFNG+LJabiuZxid1vhuNVqS5WWSUw4CyUBEmbGoD6p15xQ8wHc" crossorigin="anonymous">
</head>

<body>
    <div class="wrapper">
        <nav id="sidebar" class="sidebar js-sidebar">
            <div class="sidebar-content js-simplebar">
                <a class="sidebar-brand" href="{{ url('/') }}">
                    <span class="align-middle">Sistem Absensi Siswa MTs Darul Ishlah</span>
                </a>

                <ul class="sidebar-nav">
                    <li class="sidebar-item {{ request()->is('/') ? 'active' : '' }}">
                        <a class="sidebar-link" href="{{ url('/') }}">
                            <i class="align-middle" data-feather="sliders"></i> <span class="align-middle">Dashboard</span>
                        </a>
                    </li>
                    
                    @auth
                        @if(auth()->user()->role == 'admin')
                            <li class="sidebar-item {{ request()->is('guru') ? 'active' : '' }}">
                                <a class="sidebar-link" href="{{ url('guru') }}">
                                    <i class="align-middle" data-feather="user"></i> <span class="align-middle">Guru</span>
                                </a>
                            </li>

                            <li class="sidebar-item {{ request()->is('mapel') ? 'active' : '' }}">
                                <a class="sidebar-link" href="{{ url('mapel') }}">
                                    <i class="align-middle" data-feather="book-open"></i> <span class="align-middle">Mata Pelajaran</span>
                                </a>
                            </li>

                            <li class="sidebar-item {{ request()->is('jadwal') ? 'active' : '' }}">
                                <a class="sidebar-link" href="{{ url('jadwal') }}">
                                    <i class="align-middle" data-feather="calendar"></i> <span class="align-middle">Jadwal Pelajaran</span>
                                </a>
                            </li>
                            
                            <li class="sidebar-item {{ request()->is('kelas') ? 'active' : '' }}">
                                <a class="sidebar-link" href="{{ url('kelas') }}">
                                    <i class="align-middle" data-feather="clipboard"></i> <span class="align-middle">Kelas</span>
                                </a>
                            </li>
                            
                            <li class="sidebar-item {{ request()->is('siswa') ? 'active' : '' }}">
                                <a class="sidebar-link" href="{{ url('siswa') }}">
                                    <i class="align-middle" data-feather="users"></i> <span class="align-middle">Siswa</span>
                                </a>
                            </li>

                            <li class="sidebar-item {{ request()->is('semester') ? 'active' : '' }}">
                                <a class="sidebar-link" href="{{ url('semester') }}">
                                    <i class="align-middle" data-feather="tag"></i> <span class="align-middle">Semester</span>
                                </a>
                            </li>
                        @elseif(auth()->user()->role == 'guru')
                            <li class="sidebar-item {{ request()->is('absensi*') ? 'active' : '' }}">
                                <a class="sidebar-link" href="{{ url('absensi') }}">
                                    <i class="align-middle" data-feather="book"></i> <span class="align-middle">Absensi</span>
                                </a>
                            </li>

                            <li class="sidebar-item">
                                <a data-bs-target="#rekap-menu" data-bs-toggle="collapse" class="sidebar-link collapsed">
                                    <i class="align-middle {{ request()->is('rekap*') ? 'text-white' : '' }}" data-feather="list"></i> 
                                    <span class="align-middle {{ request()->is('rekap*') ? 'text-white' : '' }}">Rekap Absensi</span>
                                </a>
                                <ul id="rekap-menu" class="sidebar-dropdown list-unstyled collapse {{ request()->is('rekap*') ? 'show' : '' }}" data-bs-parent="#sidebar">
                                    <li class="sidebar-item {{ request()->routeIs('rekap.mapel') ? 'active' : '' }}" style="padding-left: 1.5rem">
                                        <a class="sidebar-link" href="{{ route('rekap.mapel') }}">
                                            <i class="align-middle" data-feather="file-text"></i>
                                            <span class="align-middle">Rekap Mapel</span>
                                        </a>
                                    </li>
                                    <li class="sidebar-item {{ request()->routeIs('rekap.kelas') ? 'active' : '' }}" style="padding-left: 1.5rem">
                                        <a class="sidebar-link" href="{{ route('rekap.kelas') }}">
                                            <i class="align-middle" data-feather="file-text"></i>
                                            <span class="align-middle">Rekap Kelas</span>
                                        </a>
                                    </li>
                                </ul>
                            </li>
                        @endif
                        
                        <li class="sidebar-item">
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="sidebar-link border-0">
                                    <i class="align-middle" data-feather="log-out"></i> <span class="align-middle">Logout</span>
                                </button>
                            </form>
                        </li>
                    @endauth
                </ul>
            </div>
        </nav>

        <div class="main">
            <nav class="navbar navbar-expand navbar-light navbar-bg">
                <a class="sidebar-toggle js-sidebar-toggle">
                    <i class="hamburger align-self-center"></i>
                </a>

                <div class="navbar-collapse collapse">
                    <ul class="navbar-nav navbar-align">
                        @auth
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
                                <span class="text-dark text-md">
                                    @if(auth()->user()->role === 'admin')
                                        Admin
                                    @elseif(auth()->user()->role === 'guru')
                                        {{ auth()->user()->guru->nama }}
                                    @endif
                                </span>
                            </a>
                            <div class="dropdown-menu dropdown-menu-end">
                                <a class="dropdown-item text-dark" href="{{ url('profil') }}"><i class="align-middle me-1 text-dark" data-feather="user"></i> Profile</a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item">
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="border-0 p-0" style="background: transparent">
                                            <i class="align-middle me-1" data-feather="log-out"></i> <span class="align-middle">Logout</span>
                                        </button>
                                    </form>
                                </a>
                            </div>
                        </li>
                        @endauth
                    </ul>
                </div>
            </nav>

            <main class="content">
                <div class="container-fluid p-0">
                    @yield('content')
                </div>
            </main>
        </div>
    </div>

    <!-- Scripts -->
    <script src="{{ asset('assets/static/js/app.js') }}"></script>
	<script src="https://cdn.datatables.net/v/bs5/jq-3.7.0/dt-2.2.2/r-3.0.4/datatables.min.js" integrity="sha384-XSjHFSYaGwHl3qYWCkFWjyK0GzzHmo91V9nDdCAiJ02ZqN3LiZDE1Cb0i9gEZchw" crossorigin="anonymous"></script>
	<script>
		$(document).ready( function () {
			$('#myTable').DataTable();
		} );
	</script>
    <script>
        feather.replace();
    </script>
    <script>
        const passwordInput = document.getElementById('passwordInput');
        const togglePassword = document.getElementById('togglePassword');
        const eyeIcon = togglePassword.querySelector('i');

        togglePassword.addEventListener('click', () => {
            const type = passwordInput.type === 'password' ? 'text' : 'password';
            passwordInput.type = type;
            eyeIcon.dataset.feather = type === 'password' ? 'eye-off' : 'eye';
            feather.replace();
        });

        const confirmPasswordInput = document.getElementById('confirmPasswordInput');
        const toggleConfirmPassword = document.getElementById('toggleConfirmPassword');
        const eyeIconConfirmPassword = toggleConfirmPassword.querySelector('i'); // Mengambil ikon dari tombol toggle

        toggleConfirmPassword.addEventListener('click', () => {
            const type = confirmPasswordInput.type === 'password' ? 'text' : 'password';
            confirmPasswordInput.type = type;
            eyeIconConfirmPassword.dataset.feather = type === 'password' ? 'eye-off' : 'eye';
            feather.replace(); 
        });
    </script>
    
    
    
    @yield('scripts')
</body>
</html>