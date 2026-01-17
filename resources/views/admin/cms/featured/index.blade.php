@extends('admin.layouts.app')

@section('content')
    <div class="container-fluid">
        <h1 class="h3 mb-4 text-gray-800">Featured Astrologers</h1>

        <div class="row">
            <div class="col-md-4">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Add to Featured</h6>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.cms.featured.store') }}" method="POST">
                            @csrf
                            <div class="form-group">
                                <label>Astrologer</label>
                                <select name="astrologer_profile_id" class="form-control select2" required>
                                    <option value="">Select Astrologer...</option>
                                    @foreach($astrologers as $astro)
                                        <option value="{{ $astro->id }}">
                                            {{ $astro->display_name }} ({{ $astro->user->email ?? 'No user' }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Locale</label>
                                <select name="locale" class="form-control">
                                    <option value="en">English</option>
                                    <option value="hi">Hindi</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Sort Order</label>
                                <input type="number" name="sort_order" class="form-control" value="0">
                            </div>
                            <button type="submit" class="btn btn-primary btn-block">Add Featured</button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-md-8">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Current Featured List</h6>
                    </div>
                    <div class="card-body">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Astrologer</th>
                                    <th>Locale</th>
                                    <th>Order</th>
                                    <th>Remove</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($featured as $item)
                                    <tr>
                                        <td>{{ $item->profile->display_name ?? 'Unknown' }}</td>
                                        <td><span class="badge badge-info">{{ strtoupper($item->locale) }}</span></td>
                                        <td>{{ $item->sort_order }}</td>
                                        <td>
                                            <form action="{{ route('admin.cms.featured.destroy', $item->id) }}" method="POST"
                                                onsubmit="return confirm('Remove?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger"><i
                                                        class="fas fa-trash"></i></button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center">None.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection