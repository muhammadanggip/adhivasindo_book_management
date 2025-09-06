<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Jobs\SendBookLoanNotification;
use App\Models\Book;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BookLoanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = DB::table('book_loans')
            ->join('users', 'book_loans.user_id', '=', 'users.id')
            ->join('books', 'book_loans.book_id', '=', 'books.id')
            ->select(
                'book_loans.*',
                'users.name as user_name',
                'users.email as user_email',
                'books.title as book_title',
                'books.author as book_author'
            );

        if ($request->has('user_id') && $request->user_id) {
            $query->where('book_loans.user_id', $request->user_id);
        }

        $loans = $query->paginate(15);

        return view('loans.index', compact('loans'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $users = User::all();

        // Get books that have available stock (considering current loans)
        $books = Book::where('stock', '>', 0)
            ->get()
            ->filter(function ($book) {
                return $book->available_stock > 0;
            });

        return view('loans.create', compact('users', 'books'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'book_id' => 'required|exists:books,id',
            'expected_return_at' => 'nullable|date|after_or_equal:today',
        ]);

        $book = Book::findOrFail($validated['book_id']);
        $user = User::findOrFail($validated['user_id']);

        // Check if book has available stock (considering current loans)
        if ($book->available_stock <= 0) {
            return redirect()->back()
                ->with('error', 'Book is not available for loan. All copies are currently borrowed.');
        }

        // Check if user already has this book borrowed
        $existingLoan = $user->books()
            ->where('book_id', $book->id)
            ->wherePivotNull('returned_at')
            ->exists();

        if ($existingLoan) {
            return redirect()->back()
                ->with('error', 'User already has this book borrowed');
        }

        // Create the loan
        $user->books()->attach($book->id, [
            'loaned_at' => now(),
            'expected_return_at' => $validated['expected_return_at'] ?? null,
        ]);

        // Dispatch the notification job
        SendBookLoanNotification::dispatch($user, $book);

        return redirect()->route('loans.index')
            ->with('success', 'Book loaned successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $loan = DB::table('book_loans')
            ->join('users', 'book_loans.user_id', '=', 'users.id')
            ->join('books', 'book_loans.book_id', '=', 'books.id')
            ->select(
                'book_loans.*',
                'users.name as user_name',
                'users.email as user_email',
                'books.title as book_title',
                'books.author as book_author'
            )
            ->where('book_loans.id', $id)
            ->first();

        if (!$loan) {
            return redirect()->route('loans.index')
                ->with('error', 'Loan not found');
        }

        return view('loans.show', compact('loan'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $loan = DB::table('book_loans')
            ->join('users', 'book_loans.user_id', '=', 'users.id')
            ->join('books', 'book_loans.book_id', '=', 'books.id')
            ->select(
                'book_loans.*',
                'users.name as user_name',
                'users.email as user_email',
                'books.title as book_title',
                'books.author as book_author'
            )
            ->where('book_loans.id', $id)
            ->first();

        if (!$loan) {
            return redirect()->route('loans.index')
                ->with('error', 'Loan not found');
        }

        return view('loans.edit', compact('loan'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $loan = DB::table('book_loans')->where('id', $id)->first();

        if (!$loan) {
            return redirect()->route('loans.index')
                ->with('error', 'Loan not found');
        }

        // If loan is already returned, don't allow changes
        if ($loan->returned_at) {
            return redirect()->route('loans.index')
                ->with('error', 'This loan has already been returned and cannot be modified.');
        }

        // Mark as returned
        DB::table('book_loans')
            ->where('id', $id)
            ->update([
                'returned_at' => now(),
                'updated_at' => now(),
            ]);

        return redirect()->route('loans.index')
            ->with('success', 'Book marked as returned successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $loan = DB::table('book_loans')->where('id', $id)->first();

        if (!$loan) {
            return redirect()->route('loans.index')
                ->with('error', 'Loan not found');
        }

        DB::table('book_loans')->where('id', $id)->delete();

        return redirect()->route('loans.index')
            ->with('success', 'Loan deleted successfully!');
    }

    /**
     * Get loans for a specific user.
     */
    public function userLoans(int $userId)
    {
        $user = User::findOrFail($userId);

        $loans = $user->books()
            ->withPivot(['loaned_at', 'returned_at'])
            ->get()
            ->map(function ($book) {
                return [
                    'id' => $book->pivot->id,
                    'book_id' => $book->id,
                    'title' => $book->title,
                    'author' => $book->author,
                    'isbn' => $book->isbn,
                    'loaned_at' => $book->pivot->loaned_at,
                    'returned_at' => $book->pivot->returned_at,
                    'is_returned' => !is_null($book->pivot->returned_at),
                ];
            });

        return view('loans.user-loans', compact('user', 'loans'));
    }
}
