<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Notifications\CustomNotificationByEmail;

class NotificationController extends Controller
{
    public static function sendTestEmailNotification($user)
    {
        $msg = 'Welcome to '.env('APP_NAME').'.<br>
                This is a test mail.<br>
                You can ignore for now, as we are still on development stage.';
        try {
            // $user->notify(new CustomNotificationByEmail('Welcome to '.env('APP_NAME'), $msg));
        } catch (\Exception $e) {  // Capture the exception in $e
            Log::error('Email sending failed: ' . $e->getMessage(), [
                'exception' => $e
            ]);
        }
    }

    public static function sendDepositNotification($user, $amount)
    {
        $msg = 'Your deposit request of '.$user->currency->sign.number_format($amount, 2).' has been received.<br>
                We are processing your request and will notify you once it is completed.';
        
        try {
            // $user->notify(new CustomNotificationByEmail('Deposit Request Received', $msg));
        } catch (\Exception $e) {
            Log::error('Deposit notification email sending failed: ' . $e->getMessage(), [
                'exception' => $e
            ]);
        }
    }

    public static function sendWithdrawalNotification($user, $amount)
    {
        $msg = 'Your withdrawal request of '.$user->currency->sign.number_format($amount, 2).' has been received.<br>
                We are processing your request and will notify you once it is completed.';
        
        try {
            // $user->notify(new CustomNotificationByEmail('Withdrawal Request Received', $msg));
        } catch (\Exception $e) {
            Log::error('Withdrawal notification email sending failed: ' . $e->getMessage(), [
                'exception' => $e
            ]);
        }
    }

    public static function sendTransferNotification($user, $amount, $from, $to)
    {
        $msg = 'Your transfer of ' .$user->currency->sign . number_format($amount, 2) . ' from <b>' . $from . '</b> to <b>' . $to . '</b> has been successfully processed.<br>
                If you did not initiate this transfer, please contact our support team immediately.';
        try {
            // $user->notify(new CustomNotificationByEmail('Transfer Successful', $msg));
        } catch (\Exception $e) {
            Log::error('Transfer notification email sending failed: ' . $e->getMessage(), [
                'exception' => $e
            ]);
        }
    }

    public static function sendApprovedDepositNotification($user, $amount)
    {
        $msg = 'Your deposit of ' . $user->currency->sign . number_format($amount, 2) . ' has been successfully approved and credited to your account.<br>
                Thank you for using our service!';

        try {
            // $user->notify(new CustomNotificationByEmail('Deposit Approved', $msg));
        } catch (\Exception $e) {
            Log::error('Approved deposit notification email sending failed: ' . $e->getMessage(), [
                'exception' => $e
            ]);
        }
    }

    public static function sendDeclinedDepositNotification($user, $amount, $reason = null)
    {
        $msg = 'Your deposit request of ' . $user->currency->sign . number_format($amount, 2) . ' has been declined.';

        if ($reason) {
            $msg .= '<br>Reason: ' . $reason;
        }

        $msg .= '<br>If you have any questions, please contact our support team.';

        try {
            // $user->notify(new CustomNotificationByEmail('Deposit Declined', $msg));
        } catch (\Exception $e) {
            Log::error('Declined deposit notification email sending failed: ' . $e->getMessage(), [
                'exception' => $e
            ]);
        }
    }

    public static function sendApprovedWithdrawalNotification($user, $amount)
    {
        $msg = 'Your withdrawal request of ' . $user->currency->sign . number_format($amount, 2) . ' has been successfully approved.<br>
                The funds has been processed for payout.';

        try {
            // $user->notify(new CustomNotificationByEmail('Withdrawal Approved', $msg));
        } catch (\Exception $e) {
            Log::error('Approved withdrawal notification email sending failed: ' . $e->getMessage(), [
                'exception' => $e
            ]);
        }
    }

    public static function sendDeclinedWithdrawalNotification($user, $amount, $reason = null)
    {
        $msg = 'Your withdrawal request of ' . $user->currency->sign . number_format($amount, 2) . ' has been declined.';

        if ($reason) {
            $msg .= '<br>Reason: ' . $reason;
        }

        $msg .= '<br>If you have any questions, please contact our support team.';

        try {
            // $user->notify(new CustomNotificationByEmail('Withdrawal Declined', $msg));
        } catch (\Exception $e) {
            Log::error('Declined withdrawal notification email sending failed: ' . $e->getMessage(), [
                'exception' => $e
            ]);
        }
    }

    public static function sendPositionOpenedNotification($user, $position, $asset, $wallet)
    {
        $msg = 'Your position for <b>' . $asset->name . '</b> has been successfully opened.<br><br>
                <b>Details:</b><br>
                - Asset: ' . $asset->name . ' (' . $asset->symbol . ')<br>
                - Quantity: ' . $position->quantity . '<br>
                - Price per unit: ' . $user->currency->sign . number_format($asset->price, 2) . '<br>
                - Total Amount: ' . $user->currency->sign . number_format($position->amount, 2) . '<br>
                - Account: ' . ucfirst($wallet) . '<br>
                - Status: Open<br><br>
                Thank you for trading with us!';

        try {
            // $user->notify(new CustomNotificationByEmail('Position Opened', $msg));
        } catch (\Exception $e) {
            Log::error('Position opened notification email sending failed: ' . $e->getMessage(), [
                'exception' => $e
            ]);
        }
    }

    public static function sendPositionClosedNotification($user, $position, $asset, $wallet, $closedQuantity, $pl, $plPercentage)
    {
        $msg = 'Your position for <b>' . $asset->name . '</b> has been successfully closed.<br><br>
                <b>Details:</b><br>
                - Asset: ' . $asset->name . ' (' . $asset->symbol . ')<br>
                - Closed Quantity: ' . $closedQuantity . '<br>
                - Price per unit: ' . $user->currency->sign . number_format($asset->price, 2) . '<br>
                - Total Amount: ' . $user->currency->sign . number_format($position->amount, 2) . '<br>
                - Account: ' . ucfirst($wallet) . '<br>
                - Profit/Loss: ' . $user->currency->sign . number_format($pl, 2) . ' (' . number_format($plPercentage, 2) . '%)<br>
                - Status: Closed<br><br>
                Thank you for trading with us!';

        try {
            // $user->notify(new CustomNotificationByEmail('Position Closed', $msg));
        } catch (\Exception $e) {
            Log::error('Position closed notification email sending failed: ' . $e->getMessage(), [
                'exception' => $e
            ]);
        }
    }

    public static function sendSavingsCreditNotification($user, $savingsAccount, $amount, $newBalance)
    {
        $msg = 'Your savings account <b>' . $savingsAccount->name . '</b> has been credited.<br><br>
                <b>Details:</b><br>
                - Type: Credit<br>
                - Amount: ' . $user->currency->sign . number_format($amount, 2) . '<br>
                - New Balance: ' . $user->currency->sign . number_format($newBalance, 2) . '<br><br>
                Thank you for using our savings service!';

        try {
            // $user->notify(new CustomNotificationByEmail('Savings Account Credited', $msg));
        } catch (\Exception $e) {
            Log::error('Savings credit notification email sending failed: ' . $e->getMessage(), [
                'exception' => $e
            ]);
        }
    }

    public static function sendSavingsDebitNotification($user, $savingsAccount, $amount, $newBalance)
    {
        $msg = 'Your savings account <b>' . $savingsAccount->name . '</b> has been debited.<br><br>
                <b>Details:</b><br>
                - Transaction Type: Debit<br>
                - Amount: ' . $user->currency->sign . number_format($amount, 2) . '<br>
                - New Balance: ' . $user->currency->sign . number_format($newBalance, 2) . '<br><br>
                Thank you for using our savings service!';

        try {
            // $user->notify(new CustomNotificationByEmail('Savings Account Debited', $msg));
        } catch (\Exception $e) {
            Log::error('Savings debit notification email sending failed: ' . $e->getMessage(), [
                'exception' => $e
            ]);
        }
    }
}
