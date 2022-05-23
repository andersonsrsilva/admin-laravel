@extends('admin.layouts.admin')

@section('content')

    <div class="page-title">
        <div class="title_left">
            <h1 class="h3">Usuários</h1>
        </div>
        <div class="title_right">
            <div class="pull-right">
                <ol class="breadcrumb">
                    <li><a href="/admin">Home</a></li>
                    <li class="active">Usuários</li>
                </ol>
            </div>
        </div>
    </div>

    <div class="row">
        <table class="table table-striped table-bordered dt-responsive nowrap" cellspacing="0"
               width="100%">
            <thead>
            <tr>
                <th>@sortablelink('email', 'E-mail', ['page' => $users->currentPage()])</th>
                <th>@sortablelink('name',  'Nome', ['page' => $users->currentPage()])</th>
                <th>Perfil de acesso</th>
                <th>@sortablelink('active', 'Ativo', ['page' => $users->currentPage()])</th>
                <th>@sortablelink('created_at', 'Criado em', ['page' => $users->currentPage()])</th>
                <th>@sortablelink('last_login', 'Último login', ['page' => $users->currentPage()])</th>
                <th>Ação</th>
            </tr>
            </thead>
            <tbody>
            @foreach($users as $user)
                <tr>
                    <td>{{ $user->email }}</td>
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->roles->pluck('name')->implode(',') }}</td>
                    <td>
                        @if($user->active)
                            <span class="label label-primary">{{ __('views.admin.users.index.active') }}</span>
                        @else
                            <span class="label label-danger">{{ __('views.admin.users.index.inactive') }}</span>
                        @endif
                    </td>
                    <td>{{ $user->created_at }}</td>
                    <td>{{ $user->last_login }}</td>
                    <td>
                        <a class="btn btn-xs btn-primary btn-icon" href="{{ route('admin.users.show', [$user->id]) }}" title="Mostrar">
                            <i class="fa fa-eye"></i>
                        </a>
                        <a class="btn btn-xs btn-info btn-icon" href="{{ route('admin.users.edit', [$user->id]) }}" title="editar">
                            <i class="fa fa-pencil"></i>
                        </a>
                        @if(!$user->hasRole('administrator'))
                            <a href="" 
                                class="btn btn-xs btn-danger btn-icon" 
                                title="Excluir"
                                data-toggle="modal"
                                data-target="#delete"
                                data-id="{{$user->id}}"
                                data-description="{{$user->name}}">
                                <i class="fa fa-trash"></i>
                            </a>
                        @endif
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
        <div class="pull-right">
            {{ $users->links() }}
        </div>
    </div>

    <div class="modal fade" id="delete" tabindex="-1" role="dialog" aria-labelledby="deleteLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteLabel">Confirmação</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Fechar"><span aria-hidden="true">&times;</span></button>
                </div>
                
                <form action="{{ route('admin.users.destroy') }}" method="post">
                    {{method_field('delete')}} {{csrf_field()}}
                    <div class="modal-body">
                        <p>Tem certeza de que deseja excluir?</p>
                        <input type="hidden" name="id" id="modal_id" value="" />
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-success">Confirmar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
