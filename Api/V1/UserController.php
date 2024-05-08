<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Model\User;
use App\Model\Book;
use App\Model\Cart;
use App\Model\Course;
use App\Model\Notes;
use App\Model\Categories;
use App\Model\Session;
use App\Model\app_update;
use App\Model\enrolled_course;
use App\Model\Order;
use App\Model\Publisher;
use App\Model\NotesOrder;
use App\Model\BookOrder;
use App\Model\Comment;
use App\CentralLogics\Helpers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserController extends Controller
{
    public function login(Request $request)
{
    $mobile = $request->input('mobile');
    $password = $request->input('password');

    if (empty($mobile) || empty($password)) {
        return response()->json([
            'success' => false,
            'message' => 'Mobile or password is empty.',
        ], 200);
    }

    // Check if a user with the given mobile number exists in the database
    $user = User::where('mobile', $mobile)->first();
    if (!$user) {
        return response()->json([
            'success' => false,
            'message' => 'Invalid mobile.',
        ], 200);
    }

    // Verify the password
   $user = User::where('password', $password)->first();
    if (!$user) {
        return response()->json([
            'success' => false,
            'message' => 'Invalid password.',
        ], 200);
    }

    return response()->json([
        'success' => true,
        'message' => 'Logged in successfully.',
        'data' => $user,
    ], 201);
}

//signin


public function Register(Request $request)
{
    $validator = Validator::make($request->all(), [
        'email' => 'required|email',
        'name' => 'required',
        'mobile' => 'required',
        'password' => 'required|min:6',
        'confirm_password' => 'required|same:password',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'success' => false,
            'message' => $validator->errors()->first(),
        ], 200);
    }

    $email = $request->input('email');
    $name = $request->input('name');
    $mobile = $request->input('mobile');
    $password = $request->input('password');
    $confirmPassword = $request->input('confirm_password');
    $referCode = Str::random(8); // Generate a random refer code

    $existingUser = User::where('mobile', $mobile)->first();
    if ($existingUser) {
        return response()->json([
            'success' => false,
            'message' => 'User already exists.',
        ], 200);
    }

    $user = new User;
    $user->email = $email;
    $user->name = $name;
    $user->mobile = $mobile;
    $user->password = $password;
    $user->refer_code = $referCode;
    $user->save();

    return response()->json([
        'success' => true,
        'message' => 'Registered successfully.',
        'data' => $user,
    ], 201);
}
//update profile
public function update_profile(Request $request)
{
    $user_id = $request->input('user_id');

    if (empty($user_id)) {
        return response()->json([
            'success' => false,
            'message' => 'User ID is empty',
        ], 400);
    }

    $user = User::find($user_id);

    if (!$user) {
        return response()->json([
            'success' => false,
            'message' => 'User not found',
        ], 404);
    }

    // Update user details based on the request data
    $user->name = $request->input('name');
    $user->email = $request->input('email');
    $user->mobile = $request->input('mobile');
    $user->password = $request->input('password');
    // Add more fields to update as needed

    // Save the updated user details
    $user->save();

    return response()->json([
        'success' => true,
        'message' => 'User details updated successfully',
        'data' => $user,
    ], 200);
}


//upload image
public function upload_image(Request $request)
{
    $user_id = $request->input('user_id');

    if (empty($user_id)) {
        return response()->json([
            'success' => false,
            'message' => 'User ID is empty',
        ], 400);
    }

    $user = User::find($user_id);

    if (!$user) {
        return response()->json([
            'success' => false,
            'message' => 'User not found',
        ], 404);
    }

    $validator = Validator::make($request->all(), [
        'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'success' => false,
            'message' => $validator->errors()->first(),
        ], 400);
    }

    $image = $request->file('image');
    if (!empty($image)) {
        // Assuming you have a valid image upload logic
        $imagePath = Helpers::upload('user/', 'png', $image);
        $user->image = $imagePath;
    }

    $user->save();

    $userDetails = [
        'image' =>  $user->image,
    ];

    return response()->json([
        'success' => true,
        'message' => 'Image upload successful',
        'data' => [$userDetails],
    ], 200);
}

  
    
  //userdetails
public function user_details(Request $request)
{    
    $user_id = $request->input('user_id');
    if(empty($user_id)){
        return response()->json([
            'success'=>false,
            'message' => 'User ID is empty',
        ], 200);
    }

    $user = User::where('id', $user_id)->first();

    if (!$user) {
        return response()->json([
            'success' => false,
            'message' => 'User not found',
        ], 404);
    }

    $userData = [
        'id' => $user->id,
        'name' => $user->name,
        'email' => $user->email,
        'mobile' => $user->mobile,
        'password' => $user->password,
        'refer_code' => $user->refer_code,
        'status' => $user->status,
        'joined_date' => $user->joined_date,
        'image' => $user->image,
    ];

    return response()->json([
        'success' => true,
        'message' => 'Details retrieved successfully',
        'data' => [$userData],
    ], 201);
}


// course update details
public function update_course(Request $request)
{
    $course_id = $request->input('course_id');
    if (empty($course_id)) {
        return response()->json([
            'success' => false,
            'message' => 'Course ID is empty',
        ], 400);
    }

    $course = Course::find($course_id);

    if (!$course) {
        return response()->json([
            'success' => false,
            'message' => 'Course not found',
        ], 404);
    }

    $name = $request->input('name');
    $image = $request->file('image');

    if (!empty($name)) {
        $course->name = $name;
    }

    if (!empty($image)) {
        // Assuming you have a valid image upload logic
        $imagePath = Helpers::upload('course/', 'png', $image);
        $course->image = $imagePath;
    }

    $course->save();

    $courseDetails = [
        'id' => $course->id,
        'name' => $course->name,
        'image' => asset('storage/app/public/course/' . $course->image),
    ];

    return response()->json([
        'success' => true,
        'message' => 'Course details updated successfully',
        'data' => $courseDetails,
    ], 200);
}


    //app_update
    public function app_update(Request $request)
{
    $app_updates = app_update::all(); // Assuming 'AppUpdate' is the correct model name

    if ($app_updates->isEmpty()) {
        return response()->json([
            "success" => false,
            'message' => "App Updates Not Found",
        ], 404);
    }

    $app_updateDetails = $app_updates->toArray();

    return response()->json([
        "success" => true,
        'message' => 'App Updates Retrieved Successfully',
        'data' => $app_updateDetails,
    ], 200);
}
    
    
    
//courselist
public function course_list(Request $request)
{
    $courses = Course::all();

    if ($courses->isEmpty()) {
        return response()->json([
            "success" => false,
            'message' => "No courses found",
        ], 404);
    }

    $responseData = [];

    foreach ($courses as $course) {
        $courseDetails = $course->toArray();

        $responseData[] = [
            'id' => $courseDetails['id'],
            'author' => $courseDetails['author'],
            'course_tittle' => $courseDetails['course_tittle'],
            'image' => asset('storage/app/public/course/' . $courseDetails['image']),
        ];
    }

    return response()->json([
        "success" => true,
        'message' => 'Courses listed successfully',
        'data' => $responseData,
    ], 200);
}

//sessionlist
public function session_list(Request $request)
{
    $course_id = $request->input('course_id');

    if (empty($course_id)) {
        return response()->json([
            'success' => false,
            'message' => 'Course ID is empty',
        ], 400);
    }

    $sessions = Session::where('course_id', $course_id)->get();

    if ($sessions->isEmpty()) {
        return response()->json([
            'success' => false,
            'message' => 'No sessions found for the given course ID',
        ], 404);
    }

    $responseData = [];

    foreach ($sessions as $session) {
        $sessionDetails = $session->toArray();

        $responseData[] = [
            'id' => $sessionDetails['id'],
            'tittle' => $sessionDetails['tittle'],
            'video_link' => $sessionDetails['video_link'],
            'video_duration' => $sessionDetails['video_duration'],
        ];
    }

    return response()->json([
        "success" => true,
        'message' => 'Sessions listed successfully',
        'data' => $responseData,
    ], 200);
}

//my course list
public function my_course_list(Request $request)
{
    $user_id = $request->input('user_id');
    $courses = null;

    if (empty($user_id)) {
        $courses = Course::all();
    } else {
        $courses = Course::where('user_id', $user_id)->get();
    }

    if ($courses->isEmpty()) {
        return response()->json([
            'success' => false,
            'message' => 'No courses found',
        ], 404);
    }

    $responseData = [];

    foreach ($courses as $course) {
        $courseDetails = $course->toArray();

        $responseData[] = [
            'id' => $courseDetails['id'],
            'author' => $courseDetails['author'],
            'course_tittle' => $courseDetails['course_tittle'],
            'image' => asset('storage/app/public/course/' . $courseDetails['image']),
        ];
    }

    return response()->json([
        "success" => true,
        'message' => 'Courses listed successfully',
        'data' => $responseData,
    ], 200);
}


//add categories
public function add_categories(Request $request)
{
    $category_id = $request->input('category_id');
    $name = $request->input('name');

    if (empty($category_id)) {
        return response()->json([
            'success' => false,
            'message' => 'Category ID is empty',
        ], 200);
    }
    if (empty($name)) {
        return response()->json([
            'success' => false,
            'message' => 'Name is empty',
        ], 200);
    }

    $existingCategory = categories::where('name', $name)
        ->orWhere('id', $category_id)
        ->first();

    if ($existingCategory) {
        return response()->json([
            'success' => false,
            'message' => 'Category already exists',
        ], 200);
    }

    $category = new categories();
    $category->name = $name;
    $category->save();

    return response()->json([
        'success' => true,
        'message' => 'Category added successfully',
        'data' => [
            'id' => $category->id,
            'name' => $category->name,
        ],
    ], 201);
}
//Bookslist api
    public function Booklist(Request $request)
    {    
        $user_id = $request->input('user_id');
    
        $books = Book::orderByDesc('created_at')->get();
    
        if (count($books) >= 1) {
            $rows = [];
    
            foreach ($books as $book) {
                $temp['id'] = $book['id'];
                $temp['name'] = $book['name'];
                $temp['sub_name'] = $book['sub_name'];
                $temp['sub_code'] = $book['sub_code'];
                $temp['department'] = $book['department'];
                $temp['year'] = $book['year'];
                $temp['publication'] = $book['publication'];
                $temp['regulation'] = $book['regulation'];
                $temp['price'] = $book['price'];
                $temp['book_image'] = asset('storage/app/public/book/' . $book->book_image);
                $temp['image'] = asset('storage/app/public/book/' . $book->image);
                $temp['document'] = asset('storage/app/public/book/book-pdf/' . $book->document);
                $temp['status'] = $book['status'];
                 $temp['payment_status'] = 0; // Manually added field
    
                if (!empty($user_id)) {
                    // Check if book is in user's cart
                    $cart_item = Cart::where('user_id', $user_id)
                                    ->where('book_id', $book['id'])
                                    ->first();
    
                    $temp['cart_status'] = $cart_item ? 1 : 0;
                } else {
                    $temp['cart_status'] = 0;
                }
    
                $rows[] = $temp;
            }
    
            return response()->json([
                "success" => true,
                'message' => 'Books Listed Successfully',
                'data' => $rows,
            ], 200);
        } else {
            return response()->json([
                "success" => false,
                'message' => "Books Not Found",
            ], 404);
        }
    }
    
    
   
    //Searchbook api
    public function Searchbook(Request $request)
    {    
        $search = $request->input('search');
        if(empty($search)){
            return response()->json([
                'success'=>false,
                'message' => 'Search parameter is empty',
            ], 200);
        }
        $books = Book::where('name', 'LIKE', "%$search%")
                    ->orWhere('sub_name', 'LIKE', "%$search%")
                    ->orWhere('sub_code', 'LIKE', "%$search%")
                    ->orWhere('publication', 'LIKE', "%$search%")
                    ->orderByDesc('created_at')->get();
        if (count($books) >= 1) {
            $bookData = array();
            foreach($books as $book){
                $temp['id'] = $book['id'];
                $temp['name'] = $book['name'];
                $temp['sub_name'] = $book['sub_name'];
                $temp['sub_code'] = $book['sub_code'];
                $temp['department'] = $book['department'];
                $temp['year'] = $book['year'];
                $temp['publication'] = $book['publication'];
                $temp['regulation'] = $book['regulation'];
                $temp['price'] = $book['price'];
                $temp['book_image'] = asset('storage/app/public/book/' . $book->book_image);
                $temp['image'] = asset('storage/app/public/book/' . $book->image);
                $temp['document'] = asset('storage/app/public/book/book-pdf/' . $book->document);
                $temp['status'] = $book['status'];
                $rows[]=$temp;
            }
            return response()->json([
                "success" => true,
                'message' => 'Books Retrieved Successfully',
                'data' => $rows,
            ], 201);
        } else {
            return response()->json([
                "success" => false,
                'message' => "Books Not Found",
            ], 400);
        }
    }
    
    
        //add-to cart
    public function add_cart(Request $request) {
     
        $user_id = $request->input('user_id');
        $book_id = $request->input('book_id');

        if(empty($user_id)){
                return response()->json([
                    'success'=>false,
                    'message' => 'User Id is Empty',
                ], 200);
        }
        if(empty($book_id)){
            return response()->json([
                'success'=>false,
                'message' => 'Book Id is Empty',
            ], 200);
        }

        $cartExists = Cart::where('user_id', $user_id)
                            ->where('book_id', $book_id)->exists();
        if ($cartExists) {
            return response()->json([
                "success" => false ,
                'message' => 'This Book Already Exists in Cart'

            ], 400);
        }
        else{
            $cart = new Cart;
            $cart->user_id = $request->user_id;
            $cart->book_id = $request->book_id;
            $cart->save();
        
            return response()->json([
                "success" => true ,
                'message'=> "Successfully Added to Cart",
            ], 201);
        }
    }


    //remove cart 
    public function delete_cart(Request $request) {
    
        $cart_id = $request->input('cart_id');
        $book_id = $request->input('book_id');

        if(empty($cart_id)){
                return response()->json([
                    'success'=>false,
                    'message' => 'Cart Id is Empty',
                ], 200);
        }
         $cart=Cart::where('id',$cart_id)->delete();
            
        return response()->json([
            "success" => true ,
            'message'=> "Book Removed Successfully",
        ], 201);
    }


    //cartlist
    public function Cartlist(Request $request) {
        $user_id = $request->input('user_id');
        if(empty($user_id)){
            return response()->json([
                'success' => false,
                'message' => 'User Id is Empty',
            ], 200);
        }

        $carts = DB::table('cart')
            ->join('books', 'cart.book_id', '=', 'books.id')
            ->join('users', 'users.id', '=', 'cart.user_id')
            ->select('books.*', 'cart.id AS id')
            ->where('cart.user_id', '=', $user_id)
            ->get();

        if (count($carts) >= 1) {
            $sum = 0;
            foreach ($carts as $cart) {
                $temp = new \stdClass();
                $sum += $cart->price;
                $temp->id = $cart->id;
                $temp->name = $cart->name;
                $temp->sub_name = $cart->sub_name;
                $temp->sub_code = $cart->sub_code;
                $temp->department = $cart->department;
                $temp->year = $cart->year;
                $temp->publication = $cart->publication;
                $temp->regulation = $cart->regulation;
                $temp->price = $cart->price;
                $temp->book_image = asset('storage/app/public/book/' . $cart->book_image);
                $temp->image = asset('storage/app/public/book/' . $cart->image);
                $temp->document = asset('storage/app/public/book/book-pdf/' . $cart->document);
                $temp->status = $cart->status;
                $rows[] = $temp;
            }
            return response()->json([
                "success" => true,
                'message' => 'Cart listed Successfully',
                'total_items' => count($carts),
                'total_price' => $sum,
                'data' => $rows,
            ], 201);
        } else {
            return response()->json([
                "success" => false,
                'message' => "Books Not Found",
            ], 400);
        }
    }
    //Notesslist api
    public function Noteslist(Request $request)
    {    
        $user_id = $request->input('user_id');
    
        $notes = Notes::orderByDesc('created_at')->get();
    
        if (count($notes) >= 1) {
            $rows = [];
    
            foreach ($notes as $note) {
                $temp['id'] = $note['id'];
                $temp['name'] = $note['name'];
                $temp['sub_name'] = $note['sub_name'];
                $temp['sub_code'] = $note['sub_code'];
                $temp['department'] = $note['department'];
                $temp['year'] = $note['year'];
                $temp['publication'] = $note['publication'];
                $temp['regulation'] = $note['regulation'];
                $temp['price'] = $note['price'];
                $temp['book_image'] = asset('storage/app/public/notes/' . $note->book_image);
                $temp['image'] = asset('storage/app/public/notes/' . $note->image);
                $temp['document'] = asset('storage/app/public/note/note-pdf/' . $note->document);
                $temp['status'] = $note['status'];
                $temp['payment_status'] = 0; // Manually added field
    
                if (!empty($user_id)) {
                    // Check if note is in user's cart
                    $cart_item = Cart::where('user_id', $user_id)
                                    ->where('notes_id', $note['id'])
                                    ->first();
    
                    $temp['cart_status'] = $cart_item ? 1 : 0;
                } else {
                    $temp['cart_status'] = 0;
                }
    
                $rows[] = $temp;
            }
    
            return response()->json([
                "success" => true,
                'message' => 'Notes Listed Successfully',
                'data' => $rows,
            ], 200);
        } else {
            return response()->json([
                "success" => false,
                'message' => "Notes Not Found",
            ], 404);
        }
    }
    
    
   
    //SearchNotes api
    public function SearchNotes(Request $request)
    {    
        $search = $request->input('search');
        if(empty($search)){
            return response()->json([
                'success'=>false,
                'message' => 'Search parameter is empty',
            ], 200);
        }
        $notes = Notes::where('name', 'LIKE', "%$search%")
                    ->orWhere('sub_name', 'LIKE', "%$search%")
                    ->orWhere('sub_code', 'LIKE', "%$search%")
                    ->orWhere('publication', 'LIKE', "%$search%")
                    ->orderByDesc('created_at')->get();
        if (count($notes) >= 1) {
            $notesData = array();
            foreach($notes as $note){
                $temp['id'] = $note['id'];
                $temp['name'] = $note['name'];
                $temp['sub_name'] = $note['sub_name'];
                $temp['sub_code'] = $note['sub_code'];
                $temp['department'] = $note['department'];
                $temp['year'] = $note['year'];
                $temp['publication'] = $note['publication'];
                $temp['regulation'] = $note['regulation'];
                $temp['price'] = $note['price'];
                $temp['book_image'] = asset('storage/app/public/notes/' . $note->book_image);
                $temp['image'] = asset('storage/app/public/notes/' . $note->image);
                $temp['document'] = asset('storage/app/public/note/note-pdf/' . $note->document);
                $temp['status'] = $note['status'];
                $rows[]=$temp;
            }
            return response()->json([
                "success" => true,
                'message' => 'Notes Retrieved Successfully',
                'data' => $rows,
            ], 201);
        } else {
            return response()->json([
                "success" => false,
                'message' => "Notes Not Found",
            ], 400);
        }
    }
    
    
     //order your book
    public function BookOrder(Request $request)
    {
        $user_id = $request->input('user_id');
        $book_id = $request->input('book_id');
        $image = $request->file('image');
        
        if (empty($user_id)) {
            return response()->json([
                'success' => false,
                'message' => 'User Id is Empty',
            ], 200);
        }
        
        if (empty($book_id)) {
            return response()->json([
                'success' => false,
                'message' => 'Book Id is Empty',
            ], 200);
        }

        // Handle image upload
        if ($image) {
            $filename = $image->getClientOriginalName();
            $image->storeAs('public/images', $filename);
        }
        
        $book_order = BookOrder::where('user_id', $user_id)
            ->where('book_id', $book_id)
            ->first();

        if ($book_order) {
            if (empty($book_order->image)) {
                if ($image) {
                    $filename = $image->getClientOriginalName();
                    $image->storeAs('public/images', $filename);
                    $book_order->image = $filename;
                }
                $book_order->save();
            } else {
                return response()->json([
                    "success" => false ,
                    'message' => 'This book has already been ordered'
                ], 400);
            }
    
            $created_at = strtotime($book_order->created_at);
            $now = time();
            $diff_hours = ($now - $created_at) / (60 * 60); // Get difference in hours
            if ($diff_hours > 48) {
                $order->delete();
                return response()->json([
                    "success" => false,
                    'message' => 'Book Order has expired and has been deleted',
                ], 400);
            } else {
                return response()->json([
                    "success" => true,
                    'message' => 'Book order updated successfully',
                ], 200);
            }
        }
            
        else{
            $book = Book::where('id', $book_id)->get();
            $price=$book[0]['price'];
            $book_order = new BookOrder;
            $book_order->user_id = $user_id;
            $book_order->book_id = $book_id;
            $book_order->price = $price;
            // Save the image filename in the order object
            $book_order->image = $filename ?? '';
            $book_order->payment_status = 0;
            $book_order->save();
            $cart = Cart::where('user_id', $user_id)
                        ->where('book_id', $book_id)
                        ->delete();
            return response()->json([
                "success" => true,
                'message' => 'Book Ordered Successfully',
            ], 201);
        }
    }

  public function NotesOrder(Request $request)
    {
        $user_id = $request->input('user_id');
        $note_id = $request->input('note_id');
        $image = $request->file('image');
        
        if (empty($user_id)) {
            return response()->json([
                'success' => false,
                'message' => 'User Id is Empty',
            ], 200);
        }
        
        if (empty($note_id)) {
            return response()->json([
                'success' => false,
                'message' => 'Notes Id is Empty',
            ], 200);
        }

        // Handle image upload
        if ($image) {
            $filename = $image->getClientOriginalName();
            $image->storeAs('public/images', $filename);
        }
        
        $notes_order = NotesOrder::where('user_id', $user_id)
            ->where('note_id', $note_id)
            ->first();

        if ($notes_order) {
            if (empty($notes_order->image)) {
                if ($image) {
                    $filename = $image->getClientOriginalName();
                    $image->storeAs('public/images', $filename);
                    $notes_order->image = $filename;
                }
                $notes_order->save();
            } else {
                return response()->json([
                    "success" => false ,
                    'message' => 'This book has already been ordered'
                ], 400);
            }
    
            $created_at = strtotime($notes_order->created_at);
            $now = time();
            $diff_hours = ($now - $created_at) / (60 * 60); // Get difference in hours
            if ($diff_hours > 48) {
                $notes_order->delete();
                return response()->json([
                    "success" => false,
                    'message' => 'Notes Order has expired and has been deleted',
                ], 400);
            } else {
                return response()->json([
                    "success" => true,
                    'message' => 'Notes order updated successfully',
                ], 200);
            }
        }
            
        else{
            $notes = Notes::where('id', $note_id)->get();
            $price=$notes[0]['price'];
            $notes_order = new NotesOrder;
            $notes_order->user_id = $user_id;
            $notes_order->note_id = $note_id;
            $notes_order->price = $price;
            // Save the image filename in the order object
            $notes_order->image = $filename ?? '';
            $notes_order->payment_status = 0;
            $notes_order->save();
            $cart = Cart::where('user_id', $user_id)
                        ->where('note_id', $note_id)
                        ->delete();
            return response()->json([
                "success" => true,
                'message' => 'Notes Ordered Successfully',
            ], 201);
        }
    }

    //Mybooks list
    public function Mybooks(Request $request)
    {    
        $user_id = $request->input('user_id');
        $book_id = $request->input('book_id');
        if(empty($user_id)){
            return response()->json([
                'success'=>false,
                'message' => 'User Id is empty',
            ], 200);
        }
        $query = Order::join('books', 'orders.book_id', '=', 'books.id')
                        ->select('books.*', 'orders.status AS status','orders.payment_status','orders.image AS proof_image','orders.id AS id','orders.user_id','orders.book_id','books.id AS book_id')
                        ->where('orders.user_id', $request->input('user_id'));
        if (!empty($book_id)) {
            $query->where('orders.book_id', $book_id);
        }
        $books = $query->get();
        if (count($books) >= 1) {
            foreach($books as $book){
                $temp['id'] = $book['id'];
                $temp['book_id'] = $book['book_id'];
                $temp['name'] = $book['name'];
                $temp['sub_name'] = $book['sub_name'];
                $temp['sub_code'] = $book['sub_code'];
                $temp['department'] = $book['department'];
                $temp['year'] = $book['year'];
                $temp['publication'] = $book['publication'];
                $temp['regulation'] = $book['regulation'];
                $temp['price'] = $book['price'];
                $temp['book_image'] = asset('storage/app/public/book/' . $book->book_image);
                $temp['image'] = asset('storage/app/public/book/' . $book->image);
                $temp['document'] = asset('storage/app/public/book/book-pdf/' . $book->document);
                $temp['status'] = $book['status'];
                $temp['proof_image'] = asset('storage/app/public/images/' . $book->proof_image);
                if($book['proof_image']==""){
                    $temp['image_status'] = 0;
                }
                else{
                    $temp['image_status'] = 1;
                }
                $temp['payment_status'] = $book['payment_status'];
                $rows[]=$temp;
            }
            return response()->json([
                "success" => true,
                'message' => 'Books Listed Successfully',
                'data' => $rows,
            ], 201);
        } else {
            return response()->json([
                "success" => false,
                'message' => "Books Not Found",
            ], 400);
        }
    }


}
