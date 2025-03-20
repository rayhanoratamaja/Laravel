<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form Tambah Hutang</title>
    <style>
        .currency-input {
            position: relative;
        }
        .currency-input input {
            padding-left: 1rem; /* Space for currency symbol */
        }
    </style>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center">
    <div class="bg-white p-8 rounded-lg shadow-md w-full max-w-md">
        <h2 class="text-2xl font-bold mb-6 text-center text-gray-800">Form Tambah Hutang</h2>

        <!-- Success message -->
        @if(session('success'))
            <div class="bg-green-500 text-white p-4 mb-4 rounded-md">
                {{ session('success') }}
            </div>
        @endif

        <!-- Error message -->
        @if ($errors->any())
            <div class="bg-red-500 text-white p-4 mb-4 rounded-md">
                @foreach ($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        <form action="{{ route('admin.insert.transaction') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label for="norek" class="block text-sm font-medium text-gray-700">Nomor rekening:</label>
                <select name="norek" id="norek" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                    @foreach($users as $user)
                        <option value="{{ $user->rek }}">{{ $user->rek }} - {{ $user->email }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="jumlah_transaksi" class="block text-sm font-medium text-gray-700">Jumlah Transaksi:</label>
                <input type="text" name="jumlah_transaksi" id="jumlah_transaksi" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring focus:ring-indigo-200 focus:ring-opacity-50" oninput="formatCurrency(this)">
            </div>
            <div>
                <label for="deskripsi" class="block text-sm font-medium text-gray-700">Deskripsi:</label>
                <textarea name="deskripsi" id="deskripsi" rows="4" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring focus:ring-indigo-200 focus:ring-opacity-50"></textarea>
            </div>
            <div>
                <span class="block text-sm font-medium text-gray-700">Status Hutang:</span>
                <div class="mt-2 space-x-4">
                    <label class="inline-flex items-center">
                        <input type="radio" name="status_hutang" value="Lunas" class="form-radio text-indigo-600">
                        <span class="ml-2">Lunas</span>
                    </label>
                    <label class="inline-flex items-center">
                        <input type="radio" name="status_hutang" value="Hutang" checked class="form-radio text-indigo-600">
                        <span class="ml-2">Hutang</span>
                    </label>
                    <label class="inline-flex items-center">
                        <input type="radio" name="status_hutang" value="fee" class="form-radio text-indigo-600">
                        <span class="ml-2">Biaya layanan</span>
                    </label>
                </div>
            </div>
            <div>
                <label for="tanggal_transaksi" class="block text-sm font-medium text-gray-700">Tanggal Transaksi:</label>
                <input type="date" name="tanggal_transaksi" id="tanggal_transaksi" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
            </div>
            <div>
                <button type="submit" class="w-full bg-indigo-600 text-white py-2 px-4 rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">Submit</button>
            </div>
        </form>

        <div class="mt-6 flex justify-between">
            <a href="/dashboard/admin/" class="text-sm text-indigo-600 hover:text-indigo-500">Log out</a>
            <a href="/dashboard/admin/cek-utang.php" class="text-sm text-indigo-600 hover:text-indigo-500">Cek utang</a>
        </div>
    </div>

    <script>
        function formatCurrency(input) {
            let value = input.value.replace(/\D/g, ''); // Remove non-digit characters
            if (value) {
                input.value = value.replace(/\B(?=(\d{3})+(?!\d))/g, "."); // Add dots as thousand separators
            } else {
                input.value = '';
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            var today = new Date();
            var day = String(today.getDate()).padStart(2, '0');
            var month = String(today.getMonth() + 1).padStart(2, '0');
            var year = today.getFullYear();
            var todayDate = year + '-' + month + '-' + day;
            document.getElementById('tanggal_transaksi').value = todayDate;
        });
    </script>
</body>
</html>
