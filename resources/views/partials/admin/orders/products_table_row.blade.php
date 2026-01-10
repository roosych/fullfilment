@forelse($products as $p)
    <tr>
        <td>
            <div class="d-flex align-items-center">
                <span class="fw-bold text-gray-800">{{ $p['name'] }}</span>
            </div>
        </td>
        <td class="text-end">{{ $p['sku'] }}</td>
        <td class="text-end">
            @if($p['stock'] > 1)
                <span class="fw-bold text-gray-800" title="Резерв в других заказах: {{ $p['reserved'] }}">
                    {{ $p['stock'] }}{{ $p['reserved'] > 0 ? " ({$p['reserved']})" : '' }}
                </span>
            @elseif($p['stock'] === 1)
                <span class="badge badge-light-danger" title="Резерв в других заказах: {{ $p['reserved'] }}">Last one</span>
                <span class="fw-bold text-warning ms-3">1{{ $p['reserved'] > 0 ? " ({$p['reserved']})" : '' }}</span>
            @else
                <span class="badge badge-light-danger" title="Резерв в других заказах: {{ $p['reserved'] }}">Sold out</span>
                <span class="fw-bold text-danger ms-3">0{{ $p['reserved'] > 0 ? " ({$p['reserved']})" : '' }}</span>
            @endif
        </td>
        <td class="text-end">
            <div class="d-flex justify-content-end">
                <input type="number"
                       name="products[{{ $p['id'] }}][quantity]"
                       min="1"
                       max="{{ $p['stock'] }}"
                       value="{{ $oldData[$p['id']]['quantity'] ?? '' }}"
                       class="form-control text-end"
                       style="max-width: 90px;"
                       @if($p['stock'] == 0) disabled @endif>
                <input type="hidden"
                       name="products[{{ $p['id'] }}][product_id]"
                       value="{{ $p['id'] }}">
            </div>
        </td>
    </tr>
@empty
    <tr>
        <td colspan="4" class="text-center py-10">
            <div class="text-gray-500 fs-6">Нет доступных товаров</div>
        </td>
    </tr>
@endforelse
