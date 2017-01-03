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
		<input type="file" name="pic" required>
		<input type="submit" name="submit">
		
	</form>
	<br>
	<table border="1">
		<tr>
			<th>No</th>
			<th>Name</th>
			<th>View (Local)</th>
			<th>View (S3)</th>
			<th>Action</th>
		</tr>
		@foreach ($files as $key => $file)
			<tr>
				<td>{{ $key+1 }}</td>
				<td>{{ $file }}</td>
				<td><img src="{{ Storage::get($file) }}" width="100"></td>
				<td><img src="https://s3-ap-southeast-1.amazonaws.com/files-marieduo/{{ $file }}" width="100"></td>
				<td>
					<form method="post" action="{{ URL::to('delete/'.urlencode($file)) }}">
						{{ csrf_field() }}
						{{ method_field('DELETE') }}
						<input type="submit" name="submit" value="Delete">
					</form>
				</td>
			</tr>
		@endforeach
	</table>
</body>
</html>