@extends('layouts.app')

@section('content')
<style>
    #classList {
        padding-left: 70px;
        padding-right: 70px;
    }

    .subject-container {
        width: 100%;
        height: 140px;
        padding: 20px;
        background: white;
        font-weight: 800;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.6);
        border-radius: 8px;
        position: relative;
        text-align: left;
        display: flex;
        justify-content: flex-start;
        align-items: center;
        transition: box-shadow 0.3s ease;
        margin-bottom: 20px;
        transition: color 0.3s ease, border-bottom 0.3s ease;
    }

    .subject-link {
        color: #17134b;
        font-size: 16px;
        text-align: left;
        text-decoration: none;
        display: block;
        width: 100%;
    }

    #subjectList .col-12 {
        display: flex;
        justify-content: center;
        margin-bottom: 20px;
    }

    .empty-subjects {
        font-size: 18px;
        text-align: center;
        color: #777;
        margin-top: 20px;
    }

    .btn-add {
        background: #33b482;
        position: relative;
        right: 42px;
    }

    .btn-submit {
        background: #33b482;
        color: white;
    }

    .btn-add:hover, .btn-submit:hover {
        background: #259e6e;
        color: white;
    }

    @media (max-width: 768px) {
        #subjectList .col-md-3 {
            flex: 0 0 48%;
        }
    }

    @media (max-width: 480px) {
        #subjectList .col-md-3 {
            flex: 0 0 100%;
        }
    }
</style>

<div class="p-6 flex justify-end">
    <button id="enterCodeBtn" class="bg-blue-500 text-white px-4 py-2 rounded-lg btn-add" data-bs-toggle="modal" data-bs-target="#codeModal">
        Enter Code
    </button>
</div>

<!-- Bootstrap Modal for Entering Code -->
<div class="modal fade" id="codeModal" tabindex="-1" aria-labelledby="codeModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="codeModalLabel">Enter Subject Code</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="codeForm">
                    <div class="mb-3">
                        <label for="subjectCode" class="form-label">Subject Code</label>
                        <input type="text" id="subjectCode" name="subject_code" class="form-control" required maxlength="6">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" id="submitCodeBtn" class="btn btn-submit">Submit</button>
            </div>
        </div>
    </div>
</div>

<!-- List of subjects -->
<div id="classList" class="mt-6">
    <div class="row" id="subjectList">
        @if($subjects->isEmpty())
            <p class="empty-subjects">No subjects found. You are not enrolled in any subjects.</p>
        @else
            @foreach($subjects as $subject)
                <div class="col-12 col-md-3" data-id="{{ $subject->id }}">
                    <div class="subject-container">
                        <a href="/student/subject/{{ $subject->id }}" class="subject-link">
                            <strong>Subject: </strong>{{ $subject->subject_name }} <br> <strong>Code: </strong>{{ $subject->code }}
                        </a>
                    </div>
                </div>
            @endforeach
        @endif
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

<script>
    $(document).ready(function () {
        $('#submitCodeBtn').on('click', function () {
            var subjectCode = $('#subjectCode').val();

            if (!subjectCode) {
                alert('Subject code is required!');
                return;
            }

            $.ajax({
                url: '/validate-code',
                method: 'POST',
                data: {
                    subject_code: subjectCode,
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success) {
                        alert('Code accepted! You can now access the subject.');
                        window.location.reload();
                    } else {
                        alert(response.message || 'Invalid subject code. Please try again.');
                    }
                },
                error: function() {
                    console.log(xhr.responseText);
                    alert('There was an error validating the code.');
                }
            });
        });
    });
</script>
@endsection