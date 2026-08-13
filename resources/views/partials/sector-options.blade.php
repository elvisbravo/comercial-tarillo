{{-- Espera $sector (Collection de App\Sector con la relación 'zona' cargada) y, opcionalmente, $selectedId --}}
@php $zonaActual = null; $selectedId = $selectedId ?? null; @endphp
@foreach($sector as $s)
    @php $nombreZona = optional($s->zona)->nomb_zona ?? 'Sin Zona'; @endphp
    @if($nombreZona !== $zonaActual)
        @if($zonaActual !== null)
            </optgroup>
        @endif
        <optgroup label="{{ $nombreZona }}">
        @php $zonaActual = $nombreZona; @endphp
    @endif
    <option value="{{ $s->id }}" {{ (string) $selectedId === (string) $s->id ? 'selected' : '' }}>{{ $s->nomb_sec }}</option>
@endforeach
@if($zonaActual !== null)
    </optgroup>
@endif
