<?php 
dump(App\Models\Product::first()->update(['gender' => 'kiz_cocuk']));
dump(App\Models\Product::first()->gender);
