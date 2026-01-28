@extends('admin.layouts.app')

@section('title', 'Users')

@section('content')
    <div class="card shadow mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Users</h6>
            <a href="{{ route('users.create') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i> Add User
            </a>
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" width="100%">
                    <thead class="thead-light">
                        <tr>
                            <th width="50">No</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>NIP</th>
                            <th>Unit Kerja</th>
                            <th>Role</th>
                            <th>Limit</th>
                            <th width="120">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($users as $user)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $user->name }}</td>
                                <td>{{ $user->email }}</td>
                                <td>{{ $user->nip ?? '-' }}</td>
                                <td>{{ $user->unitKerja->unit_name ?? '-' }}</td>
                                <td>{{ $user->role ?? '-' }}</td>
                                <td>
                                    Rp {{ number_format($user->saldo, 0, ',', '.') }}
                                </td>

                                <td>
                                    <a href="{{ route('users.edit', $user->id) }}" class="btn btn-warning btn-sm">
                                        <i class="fas fa-edit"></i>
                                    </a>

                                    <form action="{{ route('users.destroy', $user->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        {{-- Panggil confirmDelete(event) disini --}}
                                        <button type="submit" onclick="confirmDelete(event)" class="btn btn-danger btn-sm">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted">
                                    No users found
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
