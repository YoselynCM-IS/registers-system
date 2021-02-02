<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
	<head>
	    <meta charset="utf-8">
	    <meta http-equiv="X-UA-Compatible" content="IE=edge">
	    <meta name="viewport" content="width=device-width, initial-scale=1">
	    <title>Majestic Education</title>
	</head>
	<body style="background-color: white; color: black;">
        <h2>Hola {{ $student->name }}</h2>
	    <p>{{ $message }}</p>
		@if($student->check == 'accepted' && $student->where('book','NOT LIKE','%DIGITAL%'))
			<h2>Necesitas imprimir este correo y presentarlo para poder hacerte entrega de tu libro.</h2>
		@endif
        <h2><b>{{ __("Gracias") }}!!</b></h2>
	    <h2>{{ __("Atentamente") }}</h2>
	    <h2><b>Majestic Education</b></h2>
	</body>
</html>