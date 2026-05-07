<?php

namespace App\Http\Controllers\Affiliate;

use App\Http\Controllers\Controller;
use App\Models\AffiliateRedemption;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class RedemptionController extends Controller
{
    public function index(): View
    {
        $affiliate = Auth::user()->affiliate;
        abort_unless($affiliate, 403);

        $redemptions = $affiliate->redemptions()
            ->latest()
            ->paginate(20);

        $stats = [
            'available' => $affiliate->available_balance,
            'pending' => $affiliate->redemptions()->whereIn('status', ['pending', 'processing'])->sum('amount'),
            'completed' => $affiliate->redemptions()->where('status', 'completed')->sum('amount'),
        ];

        return view('affiliate.redemptions.index', [
            'title' => 'Redemptions',
            'redemptions' => $redemptions,
            'stats' => $stats,
            'affiliate' => $affiliate,
        ]);
    }

    public function create(): View
    {
        $affiliate = Auth::user()->affiliate;
        abort_unless($affiliate, 403);

        $minimumRedemption = config('affiliate.minimum_redemption', 500);

        return view('affiliate.redemptions.create', [
            'title' => 'Redeem Earnings',
            'affiliate' => $affiliate,
            'minimumRedemption' => $minimumRedemption,
            'breadcrumbs' => [
                ['label' => 'Redemptions', 'url' => route('affiliate.redemptions.index')],
                ['label' => 'Redeem Earnings'],
            ],
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $affiliate = Auth::user()->affiliate;
        abort_unless($affiliate, 403);

        $minimumRedemption = config('affiliate.minimum_redemption', 500);

        $validated = $request->validate([
            'amount' => ['required', 'numeric', "min:{$minimumRedemption}", "max:{$affiliate->available_balance}"],
            'payout_method' => ['required', 'string', 'in:bank_transfer,paypal'],
        ]);

        // Verify payout details exist
        if ($validated['payout_method'] === 'bank_transfer' && empty($affiliate->bank_details)) {
            return back()->withErrors(['payout_method' => 'Please add your bank details in Settings first.']);
        }

        if ($validated['payout_method'] === 'paypal' && empty($affiliate->paypal_email)) {
            return back()->withErrors(['payout_method' => 'Please add your PayPal email in Settings first.']);
        }

        $payoutDetails = $validated['payout_method'] === 'bank_transfer'
            ? $affiliate->bank_details
            : ['paypal_email' => $affiliate->paypal_email];

        DB::transaction(function () use ($affiliate, $validated, $payoutDetails) {
            AffiliateRedemption::create([
                'affiliate_id' => $affiliate->id,
                'amount' => $validated['amount'],
                'payout_method' => $validated['payout_method'],
                'payout_details' => $payoutDetails,
                'status' => 'pending',
            ]);

            $affiliate->decrement('available_balance', $validated['amount']);
        });

        return redirect()->route('affiliate.redemptions.index')
            ->with('success', "Redemption of £{$validated['amount']} submitted! It will be processed within 2 working days.");
    }
}
