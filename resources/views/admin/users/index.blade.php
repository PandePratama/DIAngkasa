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
            <div class="row mb-3">
                <div class="col-md-6">
                    <form action="{{ route('users.index') }}" method="GET" class="form-inline">
                        <label class="mr-2">Show</label>
                        <select name="per_page" class="form-control form-control-sm mr-2" onchange="this.form.submit()">
                            <option value="10" {{ request('per_page') == 10 ? 'selected' : '' }}>10</option>
                            <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25</option>
                            <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                        </select>
                        <label>entries</label>
                        <input type="hidden" name="search" value="{{ request('search') }}">
                    </form>
                </div>
                <div class="col-md-6">
                    <form action="{{ route('users.index') }}" method="GET" class="form-inline float-right">
                        <div class="input-group input-group-sm">
                            <input type="text" name="search" class="form-control"
                                placeholder="Search by name, email, NIP..." value="{{ request('search') }}"
                                aria-label="Search">
                            <input type="hidden" name="per_page" value="{{ request('per_page', 10) }}">
                            <div class="input-group-append">
                                <button class="btn btn-primary" type="submit">
                                    <i class="fas fa-search"></i>
                                </button>
                                @if (request('search'))
                                    <a href="{{ route('users.index', ['per_page' => request('per_page', 10)]) }}"
                                        class="btn btn-secondary">
                                        <i class="fas fa-times"></i>
                                    </a>
                                @endif
                            </div>
                        </div>
                    </form>
                </div>
            </div>

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
                                {{-- BAGIAN 2: Penomoran yang berlanjut antar halaman --}}
                                <td>{{ $users->firstItem() + $loop->index }}</td>

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
                                        <button type="submit"
                                            onclick="return confirm('Are you sure you want to delete this user?')"
                                            class="btn btn-danger btn-sm">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted">
                                    No users found
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-between align-items-center mt-3">
                <div>
                    Showing {{ $users->firstItem() ?? 0 }} to {{ $users->lastItem() ?? 0 }} of {{ $users->total() }}
                    entries
                    @if (request('search'))
                        <span class="text-muted">(filtered from search: "{{ request('search') }}")</span>
                    @endif
                </div>
                <div>
                    {{ $users->appends(['per_page' => request('per_page', 10), 'search' => request('search')])->links() }}
                </div>
            </div>

        </div>
    </div>
@endsection
