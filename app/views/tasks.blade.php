{{ Form::open(array('route' => 'route.name', 'method' => 'POST')) }}
	<ul>
		<li>
			{{ Form::label('created_by_user_id', 'Created_by_user_id:') }}
			{{ Form::text('created_by_user_id') }}
		</li>
		<li>
			{{ Form::label('text', 'Text:') }}
			{{ Form::textarea('text') }}
		</li>
		<li>
			{{ Form::label('status', 'Status:') }}
			{{ Form::text('status') }}
		</li>
		<li>
			{{ Form::label('priority', 'Priority:') }}
			{{ Form::text('priority') }}
		</li>
		<li>
			{{ Form::submit() }}
		</li>
	</ul>
{{ Form::close() }}