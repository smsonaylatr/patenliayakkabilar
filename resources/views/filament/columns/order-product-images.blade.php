<div style="display: flex; flex-direction: column; gap: 4px; width: max-content; min-width: 68px;">
    @foreach($getState() as $item)
        <div style="display: flex; flex-direction: row; align-items: center; gap: 6px; flex-wrap: nowrap; white-space: nowrap;">
            <img
                src="{{ $item['image'] }}"
                alt="Ürün"
                style="width: 36px; height: 36px; min-width: 36px; max-width: 36px; object-fit: cover; border: 1px solid rgba(255,255,255,0.18); border-radius: 6px; transition: transform 0.25s cubic-bezier(0.34,1.56,0.64,1), box-shadow 0.25s ease; cursor: zoom-in; transform-origin: center left; position: relative; z-index: 10; flex-shrink: 0;"
                class="hover:scale-[2.8] hover:z-[9999] hover:shadow-2xl hover:rounded-xl hover:border-2 hover:border-sky-400"
            />
            <span style="background: rgba(99,102,241,0.95); color: #ffffff; font-size: 10px; font-weight: 800; padding: 1px 5px; border-radius: 5px; white-space: nowrap; flex-shrink: 0; display: inline-block; line-height: 1.3; border: 1px solid rgba(255,255,255,0.2); box-shadow: 0 1px 3px rgba(0,0,0,0.3);">
                x{{ $item['quantity'] }}
            </span>
        </div>
    @endforeach
</div>
