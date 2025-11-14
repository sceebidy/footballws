<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>⚽ Soccer Club Explorer</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-br from-green-400 to-blue-500 min-h-screen flex flex-col items-center justify-center p-6">

    <div class="bg-white/80 backdrop-blur-lg rounded-2xl shadow-xl w-full max-w-6xl p-6">
        <h1 class="text-3xl font-bold text-center mb-6 text-emerald-700">
            ⚽ Soccer Club Explorer
        </h1>

        @if(isset($error))
            <div class="bg-red-100 text-red-700 px-4 py-3 rounded mb-4 text-center">
                ⚠️ Error: {{ $error }}
            </div>
        @elseif(empty($clubs))
            <div class="text-gray-600 text-center text-lg">Tidak ada data klub ditemukan.</div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach ($clubs as $club)
                    <div class="bg-white rounded-xl shadow-md p-4 hover:shadow-2xl transition duration-300 border border-gray-100">
                        <h2 class="text-xl font-semibold text-emerald-700">{{ $club['team'] }}</h2>
                        <p class="text-sm text-gray-600">🏟️ <strong>Stadium:</strong> {{ $club['stadium'] }}</p>
                        <p class="text-sm text-gray-600">🌍 <strong>Country:</strong> {{ $club['country'] }}</p>
                        <p class="text-sm text-gray-600">👨‍🏫 <strong>Manager:</strong> {{ $club['manager'] }}</p>
                        <p class="text-sm text-gray-600">📅 <strong>Formed:</strong> {{ $club['year'] }}</p>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

</body>
</html>
