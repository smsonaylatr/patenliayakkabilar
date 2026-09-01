<div class="flex flex-col gap-1.5">
    @foreach($getState() as $image)
        <img
            src="{{ $image }}"
            alt="Ürün"
            style="width: 55px; height: 55px; object-fit: cover; border: 1px solid rgba(255,255,255,0.18); border-radius: 8px; transition: transform 0.25s cubic-bezier(0.34,1.56,0.64,1), box-shadow 0.25s ease; cursor: zoom-in; transform-origin: center left; position: relative; z-index: 10;"
            class="hover:scale-[2.6] hover:z-[9999] hover:shadow-2xl hover:rounded-xl hover:border-2 hover:border-sky-400"
        />
    @endforeach
</div>
