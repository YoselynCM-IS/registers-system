<li class="nav-item">
    <a class="nav-link" href="{{ route('reviewer.registers') }}">{{ __('Registros') }}</a>
</li>
@if(auth()->user()->id == 7)
    <li class="nav-item">
        <a class="nav-link" href="{{ route('reviewer.folios') }}">{{ __('Depósitos') }}</a>
    </li>
@endif
<li class="nav-item">
    <a class="nav-link" href="{{ route('reviewer.codes') }}">{{ __('Codigos') }}</a>
</li>
<li class="nav-item">
    <a class="nav-link" href="{{ route('reviewer.schools') }}">{{ __('Escuelas') }}</a>
</li>
<li class="nav-item">
    <a class="nav-link" href="{{ route('reviewer.books') }}">{{ __('Libros') }}</a>
</li>