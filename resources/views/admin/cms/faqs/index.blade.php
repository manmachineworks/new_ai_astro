@extends('admin.layouts.app')

@section('content')
    <div class="container-fluid">
        <h1 class="h3 mb-4 text-gray-800">Manage FAQs</h1>

        @if($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="row">
            <!-- Create Form -->
            <div class="col-md-4">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Add New FAQ</h6>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.cms.faqs.store') }}" method="POST">
                            @csrf
                            <div class="form-group">
                                <label>Question</label>
                                <textarea name="question" class="form-control" rows="2"
                                    required>{{ old('question') }}</textarea>
                            </div>
                            <div class="form-group">
                                <label>Answer (HTML)</label>
                                <textarea name="answer_html" class="form-control" rows="5"
                                    required>{{ old('answer_html') }}</textarea>
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
                            <button type="submit" class="btn btn-primary btn-block">Add FAQ</button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- List -->
            <div class="col-md-8">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">FAQs List</h6>
                    </div>
                    <div class="card-body">
                        @forelse($faqs as $faq)
                            <div class="border-bottom pb-3 mb-3">
                                <div class="d-flex justify-content-between">
                                    <h5 class="h6 font-weight-bold text-dark">{{ $faq->question }} <span
                                            class="badge badge-light border">{{ strtoupper($faq->locale) }}</span></h5>
                                    <div>
                                        <form action="{{ route('admin.cms.faqs.destroy', $faq->id) }}" method="POST"
                                            class="d-inline" onsubmit="return confirm('Delete FAQ?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger py-0">Del</button>
                                        </form>
                                    </div>
                                </div>
                                <div class="text-muted small">
                                    {!! $faq->answer_html !!}
                                </div>
                            </div>
                        @empty
                            <p class="text-center text-muted">No FAQs found.</p>
                        @endforelse

                        {{ $faqs->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection