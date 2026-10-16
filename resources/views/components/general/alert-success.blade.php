<div>
    <div class="bg-green-100 border-t-4 border-green-500 rounded-b text-green-900 px-4 py-3 shadow-md z-50 m-2" id="divAlert" role="alert">
        <div class="flex">
            <div class="py-1"><i class="fill-current h-6 w-6 text-green-500 mr-4 fas fa-check text-lg"></i></div>
            <div>
            <p class="font-bold">¡EXITO!</p>
            <p class="text-sm">Se realizó correctamente.</p>
            </div>
        </div>
    </div>
    <script>
        setTimeout(function() {
            $("#divAlert").fadeOut(500);
        },500);
    </script>
</div>
