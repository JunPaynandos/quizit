@extends('layouts.app')

@section('content')
<div class="p-6 flex justify-end">
    <!-- Button to open modal, moved to the right -->
    <button id="addClassBtn" class="text-white px-4 py-2 rounded-lg btn-add" data-bs-toggle="modal" data-bs-target="#classModal">
        + Add Subject
    </button>
</div>

<!-- Bootstrap Modal for Adding Subject -->
<div class="modal fade" id="classModal" tabindex="-1" aria-labelledby="classModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="classModalLabel">Add New Subject</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Form to add subject -->
                <form id="classForm">
                    <div class="mb-3">
                        <label for="subjectName" class="form-label">Subject Name</label>
                        <input type="text" id="subjectName" name="subject_name" class="form-control" required>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" id="insertBtn" class="btn btn-insert">Insert</button>
            </div>
        </div>
    </div>
</div>

<!-- List of created subjects -->
<div id="classList" class="mt-6 m-5">
    <div class="row" id="subjectRow">
        @foreach($subjects as $subject)
            <div class="col-3 mb-4" data-id="{{ $subject->id }}">
                <div class="subject-container text-center w-100 rounded-lg mb-2 d-flex justify-content-center align-items-center">
                    <a href="/teacher/subject/{{ $subject->id }}" class="subject-link text-decoration-none">
                        Subject: {{ $subject->subject_name }} <br> Code: {{ $subject->code }}
                    </a>
                    <svg class="bi bi-trash3" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                        <path d="M6.5 1h3a.5.5 0 0 1 .5.5v1H6v-1a.5.5 0 0 1 .5-.5M11 2.5v-1A1.5 1.5 0 0 0 9.5 0h-3A1.5 1.5 0 0 0 5 1.5v1H1.5a.5.5 0 0 0 0 1h.538l.853 10.66A2 2 0 0 0 4.885 16h6.23a2 2 0 0 0 1.994-1.84l.853-10.66h.538a.5.5 0 0 0 0-1zm1.958 1-.846 10.58a1 1 0 0 1-.997.92h-6.23a1 1 0 0 1-.997-.92L3.042 3.5zm-7.487 1a.5.5 0 0 1 .528.47l.5 8.5a.5.5 0 0 1-.998.06L5 5.03a.5.5 0 0 1 .47-.53Zm5.058 0a.5.5 0 0 1 .47.53l-.5 8.5a.5.5 0 1 1-.998-.06l.5-8.5a.5.5 0 0 1 .528-.47M8 4.5a.5.5 0 0 1 .5.5v8.5a.5.5 0 0 1-1 0V5a.5.5 0 0 1 .5-.5"/>
                    </svg>
                </div>
            </div>
        @endforeach
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

<script>
    $(document).ready(function () {
    // Handle button click for form submission with jQuery
    $('#insertBtn').on('click', function () {
        var subjectName = $('#subjectName').val();

        // Check if subject name is not empty
        if (!subjectName) {
            alert('Subject name is required!');
            return;
        }

        // Send the form data to the server using AJAX
        $.ajax({
            url: '/classes',  // The route to handle saving the class
            method: 'POST',
            data: {
                subject_name: subjectName,
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                var newClassLink = $('<a></a>')
                    .addClass('subject-link text-decoration-none')
                    .text(response.subject.subject_name + ' (Code: ' + response.subject.code + ')')
                    .attr('href', '/teacher/subject/' + response.subject.id);

                var newSubjectDiv = $('<div></div>')
                    .addClass('subject-container text-center w-100 h-100 rounded-lg mb-2 d-flex justify-content-center align-items-center position-relative')
                    .append(newClassLink);

                // Add trash icon for the new subject
                var trashIcon = $('<svg class="bi bi-trash3" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">')
                    .append($('<path>').attr('d', 'M6.5 1h3a.5.5 0 0 1 .5.5v1H6v-1a.5.5 0 0 1 .5-.5M11 2.5v-1A1.5 1.5 0 0 0 9.5 0h-3A1.5 1.5 0 0 0 5 1.5v1H1.5a.5.5 0 0 0 0 1h.538l.853 10.66A2 2 0 0 0 4.885 16h6.23a2 2 0 0 0 1.994-1.84l.853-10.66h.538a.5.5 0 0 0 0-1zm1.958 1-.846 10.58a1 1 0 0 1-.997.92h-6.23a1 1 0 0 1-.997-.92L3.042 3.5zm-7.487 1a.5.5 0 0 1 .528.47l.5 8.5a.5.5 0 0 1-.998.06L5 5.03a.5.5 0 0 1 .47-.53Zm5.058 0a.5.5 0 0 1 .47.53l-.5 8.5a.5.5 0 1 1-.998-.06l.5-8.5a.5.5 0 0 1 .528-.47M8 4.5a.5.5 0 0 1 .5.5v8.5a.5.5 0 0 1-1 0V5a.5.5 0 0 1 .5-.5'));

                // Add click handler to the trash icon to delete subject
                trashIcon.on('click', function() {
                    var subjectDiv = $(this).closest('.col-3');
                    var subjectId = subjectDiv.data('id');

                    // Perform deletion with AJAX
                    $.ajax({
                        url: '/classes/' + subjectId, // API endpoint for deleting subject
                        method: 'POST',
                        data: {
                            _method: 'DELETE',
                            _token: $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(response) {
                            // On success, remove the subject from the DOM
                            subjectDiv.remove();
                        },
                        error: function() {
                            alert('Error deleting subject');
                        }
                    });
                });

                newSubjectDiv.append(trashIcon);

                // Get the last row and its number of columns
                var lastRow = $('#classList .row').last();
                var lastRowColumns = lastRow.find('.col-3').length;

                if (lastRowColumns < 4) {
                    // If there is space in the last row, append the new subject to the last row
                    var newCol = $('<div class="col-3 mb-4">').append(newSubjectDiv);
                    lastRow.append(newCol);
                } else {
                    // IF last row is full, create a new row and append the new subject
                    var newRow = $('<div class="row">').append(
                        $('<div class="col-3 mb-4">').append(newSubjectDiv)
                    );
                    $('#classList').append(newRow);
                }

                // Hide modal and reset form
                var modal = bootstrap.Modal.getInstance('#classModal');
                modal.hide();
                $('#classForm')[0].reset();
                cleanUpModal();
            },
            error: function() {
                alert('There was an error creating the class.');
            }
        });
    });

    // Cleanup modal state after hiding it
    $('#classModal').on('hidden.bs.modal', function () {
        cleanUpModal();
    });

    function cleanUpModal() {
        $('body').removeClass('modal-open');
        $('.modal-backdrop').remove();
        $('body').get(0).offsetHeight; 
        $('body').css('overflow', 'auto');
    }
});

</script>
<style>
    .subject-container {
        width: 100%;
        height: 140px;
        text-align: left;
        display: flex;
        justify-content: flex-start;
        align-items: center;
        background: white;
        padding: 20px;
        font-weight: 800;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.6);
        position: relative;
        transition: color 0.3s ease, border-bottom 0.3s ease;
    }

    .subject-link {
        color: #17134b;
        font-size: 16px;
        text-align: left;
        display: block;
        width: 80%;
    }

    .bi-trash3 {
        position: absolute;
        right: 5px;
        bottom: 5px;
        cursor: pointer;
    }

    .btn-add {
        background: #33b482;
        position: relative;
        right: 32px;
    }

    .btn-add:hover {
        background: #259e6e;
        color: white;
    }

    .btn-insert {
        background: #33b482;
        color: white;
    }

    .btn-insert:hover {
        background: #259e6e;
        color: white;
    }

    .modal-header {
        border-bottom: none;
    }

    .modal-footer {
        border-top: none;
    }

    #subjectList {
        display: flex;
        flex-wrap: wrap;
        justify-content: space-between;
    }

    #subjectList .col-md-3 {
        width: 24%;
        box-sizing: border-box;
        margin-bottom: 20px;
    }

    @media (max-width: 768px) {
        #subjectList .col-md-3 {
            width: 48%;
        }
    }

    @media (max-width: 480px) {
        #subjectList .col-md-3 {
            width: 100%;
        }
    }
</style>
@endsection