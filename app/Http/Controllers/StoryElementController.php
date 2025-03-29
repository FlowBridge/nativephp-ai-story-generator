<?php

namespace App\Http\Controllers;

use App\Models\StoryElement;
use Illuminate\Http\Request;
use App\Models\StoryElementType;
use Prism\Prism\Prism;
use Prism\Prism\ValueObjects\Messages\SystemMessage;
use Prism\Prism\ValueObjects\Messages\UserMessage;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class StoryElementController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return StoryElement::with('type')->get();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'type_id' => 'nullable|exists:story_element_types,id',
            'new_type' => 'nullable|string|max:255',
            'new_category' => 'nullable|string|max:255',
            'image' => 'required|url'
        ]);

        // Handle new type creation if provided
        if ($request->new_type) {
            $type = StoryElementType::create([
                'name' => $request->new_type,
                'category' => $request->new_category ?? 'other'
            ]);
            $validated['type_id'] = $type->id;
        }

        // Handle image URL
        if ($request->image) {
            try {
                // Download the image
                $response = Http::timeout(10)->get($request->image);

                if (!$response->successful()) {
                    throw new \Exception('Failed to download image');
                }

                // Get file extension from content type
                $contentType = $response->header('Content-Type');
                $extension = match ($contentType) {
                    'image/jpeg', 'image/jpg' => 'jpg',
                    'image/png' => 'png',
                    'image/gif' => 'gif',
                    default => 'jpg'
                };

                // Generate unique filename
                $filename = uniqid() . '.' . $extension;

                // Save the image
                Storage::put(
                    'story-elements/' . $filename,
                    $response->body()
                );

                // Save the public URL in the database
                $validated['image'] = 'storage/story-elements/' . $filename;
            } catch (\Exception $e) {
                Log::error('Failed to process image URL', [
                    'url' => $request->image,
                    'error' => $e->getMessage()
                ]);
                return response()->json([
                    'message' => 'Failed to process image URL. Please make sure the URL is accessible and points to a valid image.'
                ], 422);
            }
        }

        $element = StoryElement::create($validated);
        return $element->load('type');
    }

    /**
     * Display the specified resource.
     */
    public function show(StoryElement $storyElement)
    {
        return $storyElement->load('type');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, StoryElement $storyElement)
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'type_id' => 'nullable|exists:story_element_types,id'
        ]);

        $storyElement->update($validated);
        return $storyElement;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(StoryElement $storyElement)
    {
        // Delete associated images if they exist
        if ($storyElement->image) {
            $filename = basename($storyElement->image);
            Storage::delete([
                'story-elements/' . $filename,
                'story-elements/thumbnails/' . $filename
            ]);
        }

        $storyElement->delete();
        return response()->noContent();
    }


    public function generateStory(Request $request)
    {
        $request->validate([
            'elements' => 'required|array|min:3',
            'elements.*' => 'integer|exists:story_elements,id',
            'language' => 'required|string|in:nl,en',
            'age' => 'required|integer|min:1|max:99'
        ]);

        try {
            $elements = StoryElement::whereIn('id', $request->elements)
                ->pluck('name')
                ->toArray();

            Log::info('Generating story with elements:', $elements);

            $language = match($request->language) {
                'nl' => 'Dutch',
                default => 'English'
            };

            $maxStoryLength = config('story.max_story_length');
            $response = Prism::text()
                ->using(
                    config('story.default_llm_provider'),
                    config('story.default_llm_model')
                )
                ->withMessages([
                    new SystemMessage("Create a light, fun, and imaginative story for a {$request->age}-year-old child. The story should include provided by the user.

                                Write the story in spoken-style {$language}, as it will be used for text-to-speech. Keep the language simple, engaging, and age-appropriate.

                                Use strong storytelling elements such as:
                                - A clear beginning, middle, and end
                                - A main character with a goal or problem
                                - Elements of tension or mild danger (e.g. a challenge, mystery, or something unexpected), suitable for young children
                                - Surprises, humor, or playful twists
                                - Friendship, teamwork, or kindness
                                - Simple dialogue that sounds natural when spoken aloud
                                - A satisfying or heartwarming ending

                                Avoid non-spoken text like descriptions or directions. The story should sound natural when read aloud and be no longer than {$maxStoryLength} characters."),
                    new UserMessage('Elements: ' . implode(', ', $elements))
                ])
                ->usingTemperature(0.4)
                ->asText();


            // Remove <think> tags and their content
            $story = preg_replace('/<think>.*?<\/think>/s', '', $response->text);
            // Escape special characters and normalize whitespace for text-to-speech
            $story = preg_replace('/\s+/', ' ', $story);
            $story = trim($story);

            Log::info('Story generated successfully', ['length' => strlen($story)]);

            if (empty($story)) {
                throw new \Exception('Generated story is empty');
            }


            Log::info('Calling ElevenLabs API for text-to-speech conversion');

            $elevenLabsBaseUrl = 'https://api.elevenlabs.io/v1/text-to-speech/';

            $voiceId = match($request->language) {
                'nl' => config('story.eleven_labs_voices.dutch_voice_id'),
                default => config('story.eleven_labs_voices.english_voice_id')
            };

            $elevenLabsResponse = Http::timeout(60)->withHeaders([
                'xi-api-key' => config('story.eleven_labs_api_key'),
                'Content-Type' => 'application/json',
            ])->post($elevenLabsBaseUrl . $voiceId . '?output_format=mp3_44100_128', [
                'text' => $story,
                'model_id' => config('story.eleven_labs_model')
            ]);

            if (!$elevenLabsResponse->successful()) {
                Log::error('ElevenLabs API error', [
                    'status' => $elevenLabsResponse->status(),
                    'body' => $elevenLabsResponse->body()
                ]);
                throw new \Exception('Failed to generate audio: ' . $elevenLabsResponse->status());
            }

            // Generate a unique filename
            $filename = 'stories/' . uniqid() . '.mp3';

            // Save the audio file to storage
            Storage::put($filename, $elevenLabsResponse->body());

            // Create a new story record
            $story = \App\Models\Story::create([
                'content' => $story,
                'audio_url' => $filename,
                'story_elements' => $request->elements
            ]);

            return response()->json([
                'story' => $story->content,
                'audio_url' => asset('storage/' . $story->audio_url)
            ]);

        } catch (\Exception $e) {
            Log::error('Error in generateStory:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'error' => 'Failed to generate story: ' . $e->getMessage()
            ], 500);
        }
    }
}
