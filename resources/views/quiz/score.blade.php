@extends('layouts.app')

@section('content')
<div class="container">
    <img src="{{ asset('images/qlf.png') }}" alt="Logo" class="logo" style="width: 8rem; height: 4rem;">
    <a href="{{ route('quiz.result', ['subjectId' => $quiz->subject_id]) }}" class="class-link" style="position: relative; top: -2.5rem;">
        &lt;Quiz
    </a>
    <br><br>

    <h1><strong>{{ $quiz->quiz_name }}</strong></h1>

    <p><strong>Total Questions:</strong> {{ $quiz->questions->count() }}</p>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Student Name</th>
                <th>Score</th>
                <th>Date Taken</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($results as $result)
                <tr>
                    <td>{{ $result->user->name }}</td>
                    <td>{{ $result->score }} / {{ $quiz->questions->count() }}</td>
                    <td>{{ $result->created_at->format('d M Y') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="3">No students answer the quiz yet.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
<style>
    img {
        display: flex;
        align-items: center;
        justify-self: center;
    }

    .class-link {
        color: gray;  /* Initial color */
        text-decoration: none;
        transition: color 0.3s ease, border-bottom 0.3s ease;
    }

    .class-link:hover {
        color: #259e6e;  /* Color on hover */
    }

    h1 {
        position: relative;
        font-size: 30px;
        top: 40px;
        bottom: 10px;
    }

    p {
        position: relative;
        font-size: 25px;
        margin: 60px 0 20px 0;
    }

    .table {

    }
</style>
@endsection
