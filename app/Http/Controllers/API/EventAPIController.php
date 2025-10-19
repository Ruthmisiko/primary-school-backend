<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateEventAPIRequest;
use App\Http\Requests\API\UpdateEventAPIRequest;
use App\Models\Event;
use App\Repositories\EventRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;

/**
 * Class EventAPIController
 */
class EventAPIController extends AppBaseController
{
    private EventRepository $eventRepository;

    public function __construct(EventRepository $eventRepo)
    {
        $this->eventRepository = $eventRepo;
    }

    /**
     * Display a listing of the Events.
     * GET|HEAD /events
     */
    public function index(Request $request): JsonResponse
    {
        $events = $this->eventRepository->all(
            $request->except(['skip', 'limit']),
            $request->get('skip'),
            $request->get('limit')
        );

        return $this->sendResponse($events->toArray(), 'Events retrieved successfully');
    }

    /**
     * Store a newly created Event in storage.
     * POST /events
     */
    public function store(CreateEventAPIRequest $request): JsonResponse
    {
        $input = $request->all();

        $input['school_id'] = auth()->user()->school_id;

        $event = $this->eventRepository->create($input);

        return $this->sendResponse($event->toArray(), 'Event saved successfully');
    }

    /**
     * Display the specified Event.
     * GET|HEAD /events/{id}
     */
    public function show($id): JsonResponse
    {
        /** @var Event $event */
        $event = $this->eventRepository->find($id);

        if (empty($event)) {
            return $this->sendError('Event not found');
        }

        return $this->sendResponse($event->toArray(), 'Event retrieved successfully');
    }

    /**
     * Update the specified Event in storage.
     * PUT/PATCH /events/{id}
     */
    public function update($id, UpdateEventAPIRequest $request): JsonResponse
    {
        $input = $request->all();

        $input['school_id'] = auth()->user()->school_id;

        /** @var Event $event */
        $event = $this->eventRepository->find($id);

        if (empty($event)) {
            return $this->sendError('Event not found');
        }

        $event = $this->eventRepository->update($input, $id);

        return $this->sendResponse($event->toArray(), 'Event updated successfully');
    }

    /**
     * Remove the specified Event from storage.
     * DELETE /events/{id}
     *
     * @throws \Exception
     */
    public function destroy($id): JsonResponse
    {
        /** @var Event $event */
        $event = $this->eventRepository->find($id);

        if (empty($event)) {
            return $this->sendError('Event not found');
        }

        $event->delete();

        return $this->sendSuccess('Event deleted successfully');
    }
}
