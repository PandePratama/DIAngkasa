@extends('admin.layouts.app')

@section('title', 'Unit Kerja')

@section('content')
<div class="card shadow mb-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h6 class="m-0 font-weight-bold text-primary">Unit Kerja</h6>
        <a href="{{ route('unit-kerja.create') }}" class="btn btn-primary btn-sm">
            <i class="fas fa-plus"></i> Add Unit Kerja
        </a>
    </div>

    <div class="card-body">
        <div class="table-responsive">

            <table class="table table-bordered table-hover" width="100%" cellspacing="0">
                <thead class="thead-light">
                    <tr>
                        <th width="50">#</th>
                        <th>Name</th>
                        <th width="150">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($unitKerja as $unit_kerja)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $unit_kerja->unit_name }}</td>
                        <td>
                            <a href="{{ route('unit-kerja.edit', $unit_kerja->id) }}"
                                class="btn btn-warning btn-sm">
                                <i class="fas fa-edit"></i>
                            </a>

                            <form action="{{ route('unit-kerja.destroy', $unit_kerja->id) }}"
                                method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button onclick="return confirm('Delete this unit kerja?')"
                                    class="btn btn-danger btn-sm">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" class="text-center text-muted">
                            No unit kerja found
                        </td>
                    </tr>
                    @endforelse
                </tbody>

            </table>
        </div>
    </div>
</div>
@endsection