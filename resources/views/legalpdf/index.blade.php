@extends('layouts.app')

@section('content')
    <div class="card shadow">
        <div class="card-header bg-primary text-white">
            <h4>Search Gmail Thread</h4>
        </div>
        <div class="card-body">
            @if(session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            <form method="POST" action="{{ route('legalpdf.search') }}">
                @csrf
                <div class="mb-3">
                    <label class="form-label">Subject</label>
                    <input type="text" name="subject" class="form-control" placeholder="Optional subject keyword">
                </div>
                <div class="mb-3">
                    <label class="form-label">From Email</label>
                    <input type="email" name="from" class="form-control" placeholder="sender@gmail.com">
                </div>
                <div class="mb-3">
                    <label class="form-label">To Email</label>
                    <input type="email" name="to" class="form-control" placeholder="recipient@gmail.com">
                </div>
                <button type="submit" class="btn btn-success">Search Thread</button>
            </form>
        </div>
    </div>
@endsection
