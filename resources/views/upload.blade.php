<!DOCTYPE html>
<html>
<head>
	<title>Upload - Laravel</title>
</head>
<body>
	<h1>Upload Form</h1>
	<form method="post" action="{{ URL::to('/upload') }}" enctype="multipart/form-data">
		{{ csrf_field() }}
		{{ method_field('POST') }}
		<input type="text" name="title" placeholder="title">
		<input type="file" name="pic">
		<input type="submit" name="submit">
		
	</form>

	<a href="/check">here</a>
</body>
</html>