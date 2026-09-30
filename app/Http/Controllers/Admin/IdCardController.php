<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\IdCard;
use App\Models\User;
use App\Traits\PerPageTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class IdCardController extends Controller
{
    use PerPageTrait;

    /**
     * Display list of ID cards
     */
    public function index(Request $request)
    {
        $perPage = $this->getPerPage($request, 10);

        $query = IdCard::with(['staff', 'authorizedSignature'])->orderByDesc('issued_at');

        if ($search = $request->get('search')) {
            $query->whereHas('staff', function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                    ->orWhere('middle_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('staff_id', 'like', "%{$search}%");
            });
        }

        $idCards = $query->paginate($perPage)->appends($request->query());

        $totalCards = IdCard::count();
        $expiredCards = IdCard::whereDate('valid_until', '<', now())->count();
        $validCards = $totalCards - $expiredCards;
        $newThisMonth = IdCard::whereMonth('created_at', now()->month)->count();
        $totalStaff = User::count();

        if ($request->wantsJson()) {
            return response()->json([
                'html' => view('admin.id-cards._results', compact('idCards'))->render(),
                'total' => $idCards->total(),
                'grand_total' => $totalCards,
            ]);
        }

        return view('admin.id-cards.index', compact(
            'idCards', 'totalCards', 'expiredCards', 'validCards', 'newThisMonth', 'totalStaff'
        ));
    }

    /**
     * Show form to generate a new ID card
     */
    public function create()
    {
        $staff = User::orderBy('first_name')->get();

        return view('admin.id-cards.create', compact('staff'));
    }

    /**
     * Store new ID card
     */
    public function store(Request $request)
    {
        $request->validate([
            'staff_id' => 'required|exists:staff,id|unique:id_cards,staff_id',
            'issued_at' => 'required|date',
            'valid_until' => 'required|date|after_or_equal:issued_at',
            // The signature drawn live at the point of collection — the staff member
            // themselves signs to confirm they are collecting the card, not an
            // administrative "authorized by" stamp.
            'signature_data' => 'required|string',
        ]);

        $collectingStaff = User::findOrFail($request->staff_id);

        $signatureData = $request->signature_data;
        if (str_contains($signatureData, ',')) {
            $signatureData = explode(',', $signatureData)[1];
        }
        $signaturePath = 'signatures/idcard_'.uniqid().'.png';
        Storage::disk('public')->put($signaturePath, base64_decode($signatureData));

        IdCard::create([
            'staff_id' => $request->staff_id,
            'issued_at' => $request->issued_at,
            'valid_until' => $request->valid_until,
            'authorized_by' => $collectingStaff->full_name,
            'signature_path' => $signaturePath,
        ]);

        return redirect()->route('admin.id-cards.index')
            ->with('success', 'ID card generated successfully.');
    }

    /**
     * Preview an ID card
     */
    public function show($id)
    {
        $idCard = IdCard::with(['staff', 'authorizedSignature'])->findOrFail($id);

        return view('admin.id-cards.preview', compact('idCard'));
    }

    /**
     * Printable view of an ID card (front + back, no admin chrome)
     */
    public function print($id)
    {
        $idCard = IdCard::with(['staff', 'authorizedSignature'])->findOrFail($id);

        return view('admin.id-cards.print', compact('idCard'));
    }

    /**
     * Delete an ID card
     */
    public function destroy($id)
    {
        $idCard = IdCard::findOrFail($id);
        $idCard->delete();

        return redirect()->route('admin.id-cards.index')
            ->with('success', 'ID card deleted successfully.');
    }
}
