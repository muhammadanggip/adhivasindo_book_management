<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\BookLoanResource;
use App\Jobs\SendBookLoanNotification;
use App\Models\Book;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use OpenApi\Annotations as OA;

/**
 * @OA\Tag(
 *     name="Book Loans",
 *     description="Book loan management endpoints"
 * )
 */
class BookLoanController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/loans",
     *     summary="Get list of book loans",
     *     description="Retrieve a paginated list of book loans with optional filters",
     *     tags={"Book Loans"},
     *     @OA\Parameter(
     *         name="user_id",
     *         in="query",
     *         description="Filter loans by user ID",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/BookLoan")),
     *             @OA\Property(property="links", type="object"),
     *             @OA\Property(property="meta", type="object")
     *         )
     *     )
     * )
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

        if ($request->has('user_id')) {
            $query->where('book_loans.user_id', $request->user_id);
        }

        $loans = $query->paginate(15);

        return BookLoanResource::collection($loans);
    }

    /**
     * @OA\Post(
     *     path="/api/loans",
     *     summary="Create a new book loan",
     *     description="Create a new book loan for a user",
     *     tags={"Book Loans"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"user_id", "book_id"},
     *             @OA\Property(property="user_id", type="integer", example=1),
     *             @OA\Property(property="book_id", type="integer", example=1),
     *             @OA\Property(property="expected_return_at", type="string", format="date-time", example="2024-01-22T10:30:00.000000Z")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Book loaned successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", ref="#/components/schemas/BookLoan")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error or book not available",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(property="errors", type="object")
     *         )
     *     )
     * )
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
            return response()->json([
                'message' => 'Book is not available for loan. All copies are currently borrowed.'
            ], 400);
        }

        // Check if user already has this book borrowed
        $existingLoan = $user->books()
            ->where('book_id', $book->id)
            ->wherePivotNull('returned_at')
            ->exists();

        if ($existingLoan) {
            return response()->json([
                'message' => 'User already has this book borrowed'
            ], 400);
        }

        // Create the loan
        $user->books()->attach($book->id, [
            'loaned_at' => now(),
            'expected_return_at' => isset($validated['expected_return_at']) && $validated['expected_return_at'] ?
                \Carbon\Carbon::parse($validated['expected_return_at'])->format('Y-m-d H:i:s') : null,
        ]);

        // Dispatch the notification job
        SendBookLoanNotification::dispatch($user, $book);

        // Get the created loan with relationships
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
            ->where('book_loans.user_id', $user->id)
            ->where('book_loans.book_id', $book->id)
            ->whereNull('book_loans.returned_at')
            ->orderBy('book_loans.created_at', 'desc')
            ->first();

        // Convert to object for BookLoanResource
        $loanObject = (object) $loan;
        return (new BookLoanResource($loanObject))
            ->response()
            ->setStatusCode(201)
            ->withHeaders([
                'message' => 'Book loaned successfully'
            ]);
    }

    /**
     * @OA\Get(
     *     path="/api/loans/{id}",
     *     summary="Get book loan by ID",
     *     description="Retrieve a specific book loan by its ID",
     *     tags={"Book Loans"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Book loan ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Book loan found",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", ref="#/components/schemas/BookLoan")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Book loan not found"
     *     )
     * )
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
            return response()->json(['message' => 'Loan not found'], 404);
        }

        return new BookLoanResource($loan);
    }

    /**
     * @OA\Put(
     *     path="/api/loans/{id}/return",
     *     summary="Mark book as returned",
     *     description="Mark a book loan as returned by setting returned_at timestamp",
     *     tags={"Book Loans"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Book loan ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Book marked as returned successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", ref="#/components/schemas/BookLoan")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Book loan not found"
     *     )
     * )
     */
    public function update(Request $request, string $id)
    {
        $loan = DB::table('book_loans')->where('id', $id)->first();

        if (!$loan) {
            return response()->json(['message' => 'Loan not found'], 404);
        }

        // If loan is already returned, don't allow changes
        if ($loan->returned_at) {
            return response()->json([
                'message' => 'This loan has already been returned and cannot be modified.'
            ], 400);
        }

        // Mark as returned
        DB::table('book_loans')
            ->where('id', $id)
            ->update([
                'returned_at' => now(),
                'updated_at' => now(),
            ]);

        // Get updated loan data
        $updatedLoan = DB::table('book_loans')
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

        return (new BookLoanResource($updatedLoan))
            ->response()
            ->withHeaders([
                'message' => 'Book marked as returned successfully'
            ]);
    }

    /**
     * @OA\Delete(
     *     path="/api/loans/{id}",
     *     summary="Delete book loan",
     *     description="Delete a book loan record",
     *     tags={"Book Loans"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Book loan ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Book loan deleted successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Book loan deleted successfully")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Book loan not found"
     *     )
     * )
     */
    public function destroy(string $id): JsonResponse
    {
        $loan = DB::table('book_loans')->where('id', $id)->first();

        if (!$loan) {
            return response()->json(['message' => 'Loan not found'], 404);
        }

        DB::table('book_loans')->where('id', $id)->delete();

        return response()->json(['message' => 'Loan deleted successfully']);
    }

    /**
     * @OA\Get(
     *     path="/api/loans/user/{userId}",
     *     summary="Get loans by user ID",
     *     description="Retrieve all book loans for a specific user",
     *     tags={"Book Loans"},
     *     @OA\Parameter(
     *         name="userId",
     *         in="path",
     *         description="User ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User loans retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/BookLoan"))
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="User not found"
     *     )
     * )
     */
    public function userLoans(int $userId)
    {
        // Check if user exists
        $user = User::find($userId);
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        // Get loans for specific user using the same query structure as index
        $loans = DB::table('book_loans')
            ->join('users', 'book_loans.user_id', '=', 'users.id')
            ->join('books', 'book_loans.book_id', '=', 'books.id')
            ->select(
                'book_loans.*',
                'users.name as user_name',
                'users.email as user_email',
                'books.title as book_title',
                'books.author as book_author'
            )
            ->where('book_loans.user_id', $userId)
            ->orderBy('book_loans.created_at', 'desc')
            ->get();

        return BookLoanResource::collection($loans);
    }
}

/**
 * @OA\Schema(
 *     schema="BookLoan",
 *     type="object",
 *     title="Book Loan",
 *     description="Book loan model",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="user_id", type="integer", example=1),
 *     @OA\Property(property="book_id", type="integer", example=1),
 *     @OA\Property(property="user_name", type="string", example="John Doe"),
 *     @OA\Property(property="user_email", type="string", example="john@example.com"),
 *     @OA\Property(property="book_title", type="string", example="Laravel: Up & Running"),
 *     @OA\Property(property="book_author", type="string", example="Matt Stauffer"),
 *     @OA\Property(property="loaned_at", type="string", format="date-time", example="2024-01-15T10:30:00.000000Z"),
 *     @OA\Property(property="expected_return_at", type="string", format="date-time", example="2024-01-22T10:30:00.000000Z"),
 *     @OA\Property(property="returned_at", type="string", format="date-time", nullable=true, example=null),
 *     @OA\Property(property="is_returned", type="boolean", example=false),
 *     @OA\Property(property="is_overdue", type="boolean", example=false),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2024-01-15T10:30:00.000000Z"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2024-01-15T10:30:00.000000Z")
 * )
 */
