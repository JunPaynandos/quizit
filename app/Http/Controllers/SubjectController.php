<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Response;
use App\Models\Subject;
use App\Models\Student;
use App\Models\File;

class SubjectController extends Controller
{
    public function store(Request $request)
    {      
        $request->validate([
            'subject_name' => 'required|string|max:255',
        ]);

        // Random 6-digit alphanumeric code
        $code = strtoupper(bin2hex(random_bytes(3)));

        // Save the subject with the authenticated user's teacher_id
        $subject = new Subject();
        $subject->subject_name = $request->subject_name;
        $subject->teacher_id = Auth::id();
        $subject->code = $code;
        $subject->save();

        return response()->json(['subject' => $subject]);
    }

    public function index()
    {
        $subjects = Subject::where('teacher_id', Auth::id())->get();
        return view('teacher.class', compact('subjects'));
    }

    // Delete a subject
    public function destroy($id)
    {
        $subject = Subject::find($id);

        if (!$subject || $subject->teacher_id !== Auth::id()) {
            return response()->json([
                'error' => 'Unauthorized or subject not found'
            ], 403);
        }

        $subject->delete();

        return response()->json([
            'message' => 'Subject deleted successfully'
        ]);
    }

    public function studentIndex()
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        // Retrieve the subjects the student is enrolled in
        $subjects = Auth::user()->subjects;

        // Pass the subjects to the view
        return view('student.class', compact('subjects'));
    }

    public function show($id)
    {
        $subject = Subject::findOrFail($id);
        return view('teacher.subject', compact('subject'));
    }

    public function showSubject($id)
    {
        $subject = Subject::findOrFail($id);
        return view('student.subject', compact('subject'));
    }

    // Method to handle file upload
    public function uploadFile(Request $request, $subjectId)
    {
        $request->validate([
            'file' => 'required|file|mimes:jpg,jpeg,png,pdf,docx,txt|max:10240',  // Validate file type and size
        ]);
        
         // Get the uploaded file
        $file = $request->file('file');
        
        // Get the original file name
        $fileName = $file->getClientOriginalName();

        // Check if the file already exists for this subject
        $existingFile = File::where('file_name', $fileName)->where('subject_id', $subjectId)->first();

        if ($existingFile) {
            return redirect()->route('subject.show', $subjectId)->with('error', 'File has already been uploaded.');
        }

        // Store the file in public
        $file = $request->file('file');
        $filePath = $file->store('subject_files', 'public');
        

        // Create a record in the 'files' table
        $newFile = File::create([
            'file_name' => $fileName,
            'file_path' => $filePath,
            'subject_id' => $subjectId,
        ]);

        return redirect()->route('subject.show', $subjectId)->with('success', 'File uploaded successfully.');
    }

    //Method to delete file
    public function deleteFile($subjectId, $fileId)
    {
        $subject = Subject::findOrFail($subjectId);
        $file = $subject->files()->findOrFail($fileId);

        // Delete the file from storage
        Storage::disk('public')->delete($file->file_path);

        // Delete the file record from the database
        $file->delete();

        return redirect()->route('subject.show', $subjectId)->with('success', 'File deleted successfully.');
    }

    // Method to display or download the file
    public function downloadFile($subjectId, $fileId)
    {
        $subject = Subject::findOrFail($subjectId);
        $file = $subject->files()->findOrFail($fileId);

        // Get the file's path
        $filePath = storage_path('app/public/' . $file->file_path);

        return response()->download($filePath, $file->file_name);
    }

    // Method to view or download the file
    public function viewFile($subjectId, $fileId)
    {
        $subject = Subject::findOrFail($subjectId);
        $file = $subject->files()->findOrFail($fileId);

        // Get the file's path
        $filePath = storage_path('app/public/' . $file->file_path);

        // Get file extension to handle file differently
        $fileExtension = pathinfo($file->file_name, PATHINFO_EXTENSION);

        // If it is  PDF, display in browser, else download
        if ($fileExtension == 'pdf') {
            return response()->file($filePath);
        } else {
            return response()->download($filePath, $file->file_name);
        }
    }

    //subject code validation
    public function validateCode(Request $request)
    {
        // Log the incoming request
        \Log::info('Validating code: ', $request->all());

        // Validate the incoming request
        $request->validate([
            'subject_code' => 'required|string|max:6',
        ]);

        // Find the subject by the provided code
        $subject = Subject::where('code', $request->subject_code)->first();

        // Log whether the subject was found
        \Log::info('Subject found: ', ['subject' => $subject]);

        // Check if the subject exists
        if ($subject) {
            // Get the authenticated user
            $user = Auth::user();

            // Check if the user is already enrolled in the subject
            if (!$user->subjects()->where('subject_id', $subject->id)->exists()) {
                // Enroll the student in the subject
                $user->subjects()->attach($subject->id);
                return response()->json(['success' => true]);
            } else {
                return response()->json(['success' => false, 'message' => 'You are already enrolled in this subject.']);
            }
        } else {
            return response()->json(['success' => false, 'message' => 'Invalid subject code.']);
        }
    }
 
    public function showStudents($subjectId)
    {
        $subject = Subject::findOrFail($subjectId);

        // Fetch the students enrolled in the subject
        $students = $subject->students;

        return view('teacher.student-list', compact('subject', 'students'));
    }

    public function studentList($subjectId)
    {
        $subject = Subject::findOrFail($subjectId);

        $students = $subject->students;

        return view('teacher.student-list', compact('subject', 'students'));
    }

    public function removeStudent($subjectId, $studentId)
    {
        $subject = Subject::findOrFail($subjectId);

        $student = Student::where('user_id', $studentId)->firstOrFail();

        $subject->students()->detach($student->id);

        return redirect()->route('subject.studentList', $subjectId)->with('success', 'Student removed successfully!');
    }  
}
