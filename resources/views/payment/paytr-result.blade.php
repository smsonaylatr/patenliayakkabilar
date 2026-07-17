<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Ödeme Sonucu</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-white text-center p-8">

    @if($status === 'success')
        <div class="text-green-600 mb-4">
            <svg class="w-16 h-16 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
        </div>
        <h2 class="text-2xl font-bold text-gray-900 mb-2">Ödemeniz Başarılı!</h2>
        <p class="text-gray-600">Lütfen bekleyin, yönlendiriliyorsunuz...</p>
        
        <script>
            // Parent pencereyi başarılı sayfasına yönlendir (iframe içinden çıkış)
            setTimeout(function() {
                window.top.location.href = "{{ route('order.success', ['order_number' => $order_number ?? 'bilinmiyor']) }}";
            }, 1500);
        </script>
    @else
        <div class="text-red-600 mb-4">
            <svg class="w-16 h-16 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
        </div>
        <h2 class="text-2xl font-bold text-gray-900 mb-2">Ödeme Başarısız!</h2>
        <p class="text-gray-600 mb-4">{{ $reason ?? 'İşlem sırasında bir hata oluştu.' }}</p>
        <button onclick="window.top.location.reload();" class="bg-black text-white px-6 py-2 rounded-lg font-medium hover:bg-gray-800 transition">Tekrar Dene</button>
    @endif

</body>
</html>
