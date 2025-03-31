<?php

namespace App\Http\Controllers\User;

use App\Models\Notification;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Spatie\QueryBuilder\QueryBuilder;
use Spatie\QueryBuilder\AllowedInclude;
use Symfony\Component\HttpFoundation\Response;
use App\Spatie\QueryBuilder\IncludeSelectFields;
use MarcinOrlowski\ResponseBuilder\ResponseBuilder;

class NotificationController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @param \App\Models\Notification $notification
     */
    public function __construct(public Notification $notification)
    {
    }

    public function index(Request $request): Response
    {
        $notification = QueryBuilder::for(
            $this->notification->query()->where('user_id', $request->user()->id)
        )
            ->allowedFields($this->notification->getQuerySelectables())
            ->allowedIncludes([
                AllowedInclude::custom('user', new IncludeSelectFields([
                    'id',
                    'first_name',
                    'last_name',
                    'email',
                    'phone',
                ])),
            ])
            ->defaultSort('-created_at')
            ->allowedSorts([
                'created_at',
                'updated_at',
            ])
            ->paginate((int) $request->per_page) 
            ->withQueryString();

        return ResponseBuilder::asSuccess()
            ->withMessage('Notifications fetched successfully')
            ->withData(['notification' => $notification])
            ->build();
    }

    public function markAsRead(Request $request, string $id): Response
    {
        $notification = $this->notification->query()
            ->where('user_id', $request->user()->id)
            ->where('id', $id)
            ->firstOrFail();

        if (is_null($notification->read_at)) {
            $notification->update(['read_at' => now()]);
        }

        return ResponseBuilder::asSuccess()
            ->withMessage('Notification marked as read')
            ->withData(['notification' => $notification])
            ->build();
    }

    public function markAllAsRead(Request $request): Response
    {
        $this->notification->query()
            ->where('user_id', $request->user()->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return ResponseBuilder::asSuccess()
            ->withMessage('All notifications marked as read')
            ->build();
    }

    public function destroy(Request $request, string $id): Response
    {
        $notification = $this->notification->query()
            ->where('user_id', $request->user()->id)
            ->where('id', $id)
            ->firstOrFail();

        $notification->delete();

        return ResponseBuilder::asSuccess()
            ->withMessage('Notification deleted successfully')
            ->build();
    }

    public function destroyAll(Request $request): Response
    {
        $this->notification->query()
            ->where('user_id', $request->user()->id)
            ->delete();

        return ResponseBuilder::asSuccess()
            ->withMessage('All notifications deleted successfully')
            ->build();
    }
}
