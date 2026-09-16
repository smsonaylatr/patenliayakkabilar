<?php

namespace App\Livewire\Admin;

use App\Models\StockMovement;
use Livewire\Component;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class StockChart extends Component
{
    public array $labels = [];
    public array $inData = [];
    public array $outData = [];

    public function mount()
    {
        $startDate = Carbon::now()->subDays(29)->startOfDay();
        $endDate = Carbon::now()->endOfDay();
        
        $movements = StockMovement::query()
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('SUM(CASE WHEN type IN ("restock", "cancel", "return") THEN quantity ELSE 0 END) as total_in'),
                DB::raw('SUM(CASE WHEN type = "sale" THEN quantity ELSE 0 END) as total_out')
            )
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->keyBy('date');

        for ($i = 29; $i >= 0; $i--) {
            $dateStr = Carbon::now()->subDays($i)->format('Y-m-d');
            $this->labels[] = Carbon::now()->subDays($i)->format('d.m');
            
            if ($movements->has($dateStr)) {
                $this->inData[] = (int) $movements[$dateStr]->total_in;
                $this->outData[] = (int) $movements[$dateStr]->total_out;
            } else {
                $this->inData[] = 0;
                $this->outData[] = 0;
            }
        }
    }

    public function render()
    {
        return view('livewire.admin.stock-chart');
    }
}
