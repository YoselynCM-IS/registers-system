<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
	<head>
	    <meta charset="utf-8">
	    <meta http-equiv="X-UA-Compatible" content="IE=edge">
	    <meta name="viewport" content="width=device-width, initial-scale=1">
	    <title>Majestic Education</title>
	</head>
	<body style="background-color: white; color: black;">
        <h2>Hola! {{ $name }}</h2>
		<p>
			Te enviamos el código para acceder al libro {{ $book }}:
		</p>
	    @if($editorial === 'MAJESTIC EDUCATION')
			@if($code2 === 'BLINK')
				<ol>
					<li>Ingresar a: https://www.blinklearning.com/home</li>
					<li>Descarga el <a href="https://dl.dropbox.com/s/kbv18zto09ky76w/Manual_alumno_blink_ME.pdf"><b>Manual</b></a> para poder registrarte.</li>
				</ol>
			@endif
			@if($code2 === 'KOT')
				<ol>
					<li>Ingresa a la URL: https://majesticeducationdigital.kotobee.com</li>
					<li>Dar clic en Redeem Code</li>
					<li>Ingresar código</li>
					<li>Clic en el botón login</li>
				</ol>
			@endif
			<b>TU CÓDIGO ES: {{ $code }}</b>
		@endif
		@if($editorial === 'EXPRESS PUBLISHING')
			<p>Ingresa a la URL: www.expressdigibooks.com</p>
			<b>TU CÓDIGO ES: {{ $code }}</b>
			<p>Descarga la <a href="https://dl.dropbox.com/s/8lwce27hv6bc69s/ep-digibooks.pdf"><b>Guia</b></a> para poder acceder al libro</p>
		@endif
		@if($editorial === 'CENGAGE')
			<b>TU CÓDIGO DE VITALSOURCE: {{ $code }}</b><br>
			<b>TU CÓDIGO DE MyELT: {{ $code2 }}</b>
			<p>Descarga los instructivos para poder acceder al libro</p>
			<ol>
				<li><a href="https://dl.dropbox.com/s/e267o0cll0auzz4/Carta%20de%20entrega%20ebook%20VitalSource.pdf">Carta de entrega ebook VitalSource</a></li>
				<li><a href="https://dl.dropbox.com/s/0ulozdfblany6g1/myELT_template.pdf">MyELT</a></li>
				<li><a href="https://dl.dropbox.com/s/7keq4kzef7kiz7h/VitalSource_template.pdf">VitalSource</a></li>
			</ol>
		@endif
		@if($editorial === 'RICHMOND')
			<p>Revisa con tu profesor para poder acceder a tu libro.</p>
			<b>TU CÓDIGO ES: {{ $code }}</b>
		@endif
	    <hr>
		<h2><b>Majestic Education</b></h2>
		Soporte: <br>
		55-4852-7955 <br>
		contacto@majesticeducationdigital.com
	</body>
</html>