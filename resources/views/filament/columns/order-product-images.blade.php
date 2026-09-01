<div class="flex flex-col gap-1.5">
    @foreach($getState() as $item)
        <div class="flex items-center gap-2">
            <img
                src="{{ $item['image'] }}"
                alt="Ürün"
                style="width: 55px; height: 55px; object-fit: cover; border: 1px solid rgba(255,255,255,0.18); border-radius: 8px; transition: transform 0.25s cubic-bezier(0.34,1.56,0.64,1), box-shadow 0.25s ease; cursor: zoom-in; transform-origin: center left; position: relative; z-index: 10;"
                class="hover:scale-[2.6] hover:z-[9999] hover:shadow-2xl hover:rounded-xl hover:border-2 hover:border-sky-400"
            />
            <span style="background: rgba(99,102,241,0.85); color: #fff; font-size: 11px; font-weight: 700; padding: 2px 7px; border-radius: 6px; white-space: nowrap;">
                x{{ $item['quantity'] }}
            </span>
        </div>
    @endforeach
</div>
