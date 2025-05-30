<div style="margin-left:auto;margin-right:auto;">
<style media="all">
    @page {
		margin: 0;
		padding:0;
	}
	*{
		margin: 0;
		padding: 0;
	}
	body{
		line-height: 1.5;
		font-family: 'DejaVuSans', 'sans-serif';
		color: #333542;
	}
	div{
		font-size: 1rem;
	}
	.gry-color *,
	.gry-color{
		color:#878f9c;
	}
	table{
		width: 100%;
	}
	table th{
		font-weight: normal;
	}
	table.padding th{
		padding: .5rem .7rem;
	}
	table.padding td{
		padding: .7rem;
	}
	table.sm-padding td{
		padding: .2rem .7rem;
	}
	.border-bottom td,
	.border-bottom th{
		border-bottom:1px solid #eceff4;
	}
	.text-left{
		text-align:left;
	}
	.text-right{
		text-align:right;
	}
	.small{
		font-size: .85rem;
	}
	.strong{
		font-weight: bold;
	}
</style>

	@php
		$logo = get_setting('header_logo');
	@endphp

	<div style="background: #eceff4;padding: 1.5rem;">
		<table>
			<tr>
				<td>
					@if($logo != null)
						<img loading="lazy" src="{{ uploaded_asset($logo) }}" height="40" style="display:inline-block;">
					@else
						<img loading="lazy" src="{{ static_asset('assets/img/logo.png') }}" height="40" style="display:inline-block;">
					@endif
				</td>
			</tr>
		</table>

	</div>

	<div style="border-bottom:1px solid #eceff4;margin: 0 1.5rem;"></div>

    <div style="padding: 1.5rem;">
		<table class="padding text-left small border-bottom">
			<thead>
                <tr class="gry-color" style="background: #eceff4;">
					<th width="20%">{{translate('Type') }}</th>
                    <th width="50%">{{translate('Name') }}</th>
                    <th width="30%">{{translate('ID') }}</th>
					<th width="30%">{{translate('Email') }}</th>
                </tr>
			</thead>
			<tbody class="strong">
                @foreach ($users as $key => $user)
	                <tr class="">
						<td>{{ $user->user_type }}</td>
						<td>{{ $user->name }}</td>
						<td>{{ $user->id }}</td>
						<td>{{ $user->email }}</td>
					</tr>
				@endforeach
            </tbody>
		</table>
	</div>

</div>
