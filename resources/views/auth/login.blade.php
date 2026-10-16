<x-layouts.guest>
    <x-auth.authentication-card>
        <x-slot name="logo">
            {{-- <x-jet-authentication-card-logo /> --}}
            <img src="{{ asset('assets/images/logo.jpeg') }}" alt="Logo de Empresa" width="400">
        </x-slot>
        <div class="card-body p-3">
            <div class="text-center mt-1">
                <h5 class="text-primary">¡Bienvenido!</h5>
                <p class="text-muted">Inicie sesión para ingresar.</p>
            </div>
            <div class="mt-4">
                <x-general.validation-errors class="mb-4" />
                <form method="POST" action="{{ route('login') }}">
                    @csrf
                    <div class="mb-3">
                        <label for="username" class="font-semibold text-gray-500">
                            <span>Usuario:</span>
                        </label>
                        <div>
                            <input type="text" name="username" placeholder="Usuario" id="username"
                                class="mt-1 focus:ring-blue-800 focus:border-blue-800 block w-full h-12 shadow-sm sm:text-sm border-gray-300 rounded-md"
                                required>
                        </div>

                        <div class="mb-3 mt-4">
                            <label for="password" class="font-semibold text-gray-600">Contraseña: </label>
                            <input type="password" name="password" placeholder="Contraseña" id="password"
                                class="mt-1 focus:ring-blue-800 focus:border-blue-800 block w-full h-12 shadow-sm sm:text-sm border-gray-300 rounded-md"
                                required autocomplete="current-password">
                            <div class="form-check mt-4">
                                <label for="remember_me" class="flex items-center">
                                    <x-jet-checkbox id="remember_me" name="remember" />
                                    <span class="ml-2 text-sm text-gray-600">{{ __('Recordarme') }}</span>
                                </label>
                            </div>
                            <div class="mt-4 text-center mx-4">
                                <x-general.button
                                    class="bg-blue-500 text-white text-center hover:bg-blue-600 active:bg-blue-800 w-full"
                                    type="submit">
                                    {{ __(' Iniciar Sesión') }}
                                </x-general.button>
                                {{-- <x-jet-button>
                                    {{ __('Iniciar Sesión') }}
                                </x-jet-button> --}}
                            </div>

                            {{-- <div class="mt-4 text-center">
                            <p class="mb-0">Don't have an account ? <a href="auth-register.html" class="fw-medium text-primary"> Signup now </a> </p>
                        </div> --}}
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <div class="mt-5 text-center">
            <p>© 2021 Garzasoft </p>
        </div>
    </x-auth.authentication-card>
</x-layouts.guest>
