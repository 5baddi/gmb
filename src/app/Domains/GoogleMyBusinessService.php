<?php

/**
 * ClnkGO
 *
 * @copyright Copyright (c) 2022, BADDI Services. (https://baddi.info)
 */

namespace BADDIServices\ClnkGO\Domains;

use Throwable;
use Exception;
use GuzzleHttp\Client;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use GuzzleHttp\Exception\GuzzleException;
use BADDIServices\ClnkGO\Services\Service;
use BADDIServices\ClnkGO\Models\ObjectValues\GoogleBusinessLocalPostObjectValue;

class GoogleMyBusinessService extends Service
{
    public const int DEFAULT_PAGINATION_LIMIT = 25;

    public const string BASE_API_URI = 'https://mybusinessaccountmanagement.googleapis.com';

    public const string ACCOUNT_LOCATIONS_ENDPOINT = 'https://mybusinessbusinessinformation.googleapis.com/v1/accounts/%s/locations?readMask=name,title,storeCode,regularHours,languageCode,phoneNumbers,categories,storefrontAddress,websiteUri,regularHours,specialHours,serviceArea,labels,adWordsLocationExtensions,latlng,openInfo,metadata,profile,relationshipData,moreHours';
    public const string LOCATION_POSTS_ENDPOINT = 'https://mybusiness.googleapis.com/v4/accounts/%s/locations/%s/localPosts';
    public const string LOCATION_POST_ENDPOINT = 'https://mybusiness.googleapis.com/v4/accounts/%s/locations/%s/localPosts/%s';
    public const string LOCATION_MEDIA_ENDPOINT = 'https://mybusiness.googleapis.com/v4/accounts/%s/locations/%s/media';
    public const string DELETE_LOCATION_MEDIA_ENDPOINT = 'https://mybusiness.googleapis.com/v4/accounts/%s/locations/%s/media/%s';
    public const string LOCATION_REVIEWS_ENDPOINT = 'https://mybusiness.googleapis.com/v4/accounts/%s/locations/%s/reviews';
    public const string LOCATION_REVIEW_ENDPOINT = 'https://mybusiness.googleapis.com/v4/accounts/%s/locations/%s/reviews/%s';
    public const string UPDATE_LOCATION_REVIEW_REPLY_ENDPOINT = 'https://mybusiness.googleapis.com/v4/accounts/%s/locations/%s/reviews/%s/reply';

    private Client $client;

    public function __construct(
        private readonly ?string $accessToken = null,
        private readonly ?string $accountId = null,
        private readonly ?string $mainLocationId = null
    ) {
        parent::__construct();

        $this->configure();
    }

    public function getBusinessAccountLocations(
        ?string $nextPageToken = null,
        int $limit = self::DEFAULT_PAGINATION_LIMIT
    ): array {
        if (empty($this->accountId)) {
            return [];
        }

        try {
            $endpoint = sprintf(
                '%s&pageSize=%d',
                sprintf(self::ACCOUNT_LOCATIONS_ENDPOINT, $this->accountId),
                $limit
            );

            if (! empty($nextPageToken)) {
                $endpoint = sprintf('%s&pageToken=%s', $endpoint, $nextPageToken);
            }

            $response = $this->client->get($endpoint);
            $results = json_decode($response->getBody()->getContents(), true);

            if ($response->getStatusCode() !== 200 || ! Arr::has($results, ['locations'])) {
                return [];
            }

            return $results;
        } catch (Throwable) {
            return [];
        }
    }

    public function getBusinessLocationPosts(
        ?string $nextPageToken = null,
        int $limit = self::DEFAULT_PAGINATION_LIMIT
    ): array {
        if (empty($this->accountId) || empty($this->mainLocationId)) {
            return [];
        }

        try {
            $endpoint = sprintf(
                '%s?pageSize=%d',
                sprintf(self::LOCATION_POSTS_ENDPOINT, $this->accountId, $this->mainLocationId),
                $limit
            );

            if (! empty($nextPageToken)) {
                $endpoint = sprintf('%s&pageToken=%s', $endpoint, $nextPageToken);
            }

            $response = $this->client->get($endpoint);
            $results = json_decode($response->getBody()->getContents(), true);

            if ($response->getStatusCode() !== 200 || ! Arr::has($results, ['localPosts'])) {
                return [];
            }

            return $results ?? [];
        } catch (Throwable) {
            return [];
        }
    }

    public function getBusinessLocationPost(string $id): array
    {
        if (empty($this->accountId) || empty($this->mainLocationId) || empty($id)) {
            return [];
        }

        try {
            $response = $this->client->get(
                sprintf(self::LOCATION_POST_ENDPOINT, $this->accountId, $this->mainLocationId, $id)
            );

            $results = json_decode($response->getBody()->getContents(), true);
            if ($response->getStatusCode() !== 200 || ! Arr::has($results, ['name'])) {
                return [];
            }

            return $results ?? [];
        } catch (Throwable) {
            return [];
        }
    }

    public function deleteBusinessLocationPost(string $id): bool
    {
        if (empty($this->accountId) || empty($this->mainLocationId) || empty($id)) {
            return false;
        }

        try {
            $response = $this->client->delete(
                sprintf(self::LOCATION_POST_ENDPOINT, $this->accountId, $this->mainLocationId, $id)
            );

            return $response->getStatusCode() === 200 ;
        } catch (Throwable) {
            return false;
        }
    }

    public function getBusinessLocationMedia(
        ?string $nextPageToken = null,
        int $limit = self::DEFAULT_PAGINATION_LIMIT
    ): array {
        if (empty($this->accountId) || empty($this->mainLocationId)) {
            return [];
        }

        try {
            $endpoint = sprintf(
                '%s?pageSize=%d',
                sprintf(self::LOCATION_MEDIA_ENDPOINT, $this->accountId, $this->mainLocationId),
                $limit
            );

            if (! empty($nextPageToken)) {
                $endpoint = sprintf('%s&pageToken=%s', $endpoint, $nextPageToken);
            }

            $response = $this->client->get($endpoint);
            $results = json_decode($response->getBody()->getContents(), true);

            if ($response->getStatusCode() !== 200 || ! Arr::has($results, ['mediaItems'])) {
                return [];
            }

            return $results ?? [];
        } catch (Throwable) {
            return [];
        }
    }

    public function createBusinessLocationMedia(array $media): bool
    {
        if (empty($this->accountId) || empty($this->mainLocationId)) {
            return false;
        }

        try {
            $response = $this->client->post(
                sprintf(self::LOCATION_MEDIA_ENDPOINT, $this->accountId, $this->mainLocationId),
                [
                    'body'  => json_encode($media),
                ]
            );

            $results = json_decode($response->getBody()->getContents(), true);

            return ! Arr::has($results, 'error') && $response->getStatusCode() === 200;
        } catch (Throwable) {
            return false;
        }
    }

    public function deleteBusinessLocationMedia(string $id): bool
    {
        if (empty($this->accountId) || empty($this->mainLocationId) || empty($id)) {
            return false;
        }

        try {
            $response = $this->client->delete(
                sprintf(self::DELETE_LOCATION_MEDIA_ENDPOINT, $this->accountId, $this->mainLocationId, $id)
            );

            return $response->getStatusCode() === 200;
        } catch (Throwable) {
            return false;
        }
    }

    public function getBusinessLocationReviews(
        ?string $nextPageToken = null,
        int $limit = self::DEFAULT_PAGINATION_LIMIT
    ): array {
        if (empty($this->accountId) || empty($this->mainLocationId)) {
            return [];
        }

        try {
            $endpoint = sprintf(
                '%s?pageSize=%d',
                sprintf(self::LOCATION_REVIEWS_ENDPOINT, $this->accountId, $this->mainLocationId),
                $limit
            );

            if (! empty($nextPageToken)) {
                $endpoint = sprintf('%s&pageToken=%s', $endpoint, $nextPageToken);
            }

            $response = $this->client->get($endpoint);
            $results = json_decode($response->getBody()->getContents(), true);

            if ($response->getStatusCode() !== 200 || ! Arr::has($results, ['reviews'])) {
                return [];
            }

            return $results ?? [];
        } catch (Throwable) {
            return [];
        }
    }

    public function getBusinessLocationReview(string $id): array
    {
        if (empty($this->accountId) || empty($this->mainLocationId)) {
            return [];
        }

        try {
            $response = $this->client->get(
                sprintf(self::LOCATION_REVIEW_ENDPOINT, $this->accountId, $this->mainLocationId, $id)
            );

            $results = json_decode($response->getBody()->getContents(), true);
            if ($response->getStatusCode() !== 200 || ! Arr::has($results, ['reviewId'])) {
                return [];
            }

            return $results ?? [];
        } catch (Throwable) {
            return [];
        }
    }

    public function updateBusinessLocationReviewReply(string $id, string $reply): array
    {
        if (empty($this->accountId) || empty($this->mainLocationId)) {
            return [];
        }

        try {
            $response = $this->client->put(
                sprintf(
                    self::UPDATE_LOCATION_REVIEW_REPLY_ENDPOINT,
                    $this->accountId,
                    $this->mainLocationId,
                    $id
                ),
                [
                    'body' => json_encode([
                        'comment' => $reply,
                    ]),
                ]
            );

            $results = json_decode($response->getBody()->getContents(), true);
            if ($response->getStatusCode() !== 200 || ! Arr::has($results, ['comment'])) {
                return [];
            }

            return $results ?? [];
        } catch (Throwable) {
            return [];
        }
    }

    /**
     * @throws GuzzleException|Exception
     */
    public function createScheduledPost(GoogleBusinessLocalPostObjectValue $values): bool
    {
        if (empty($this->accountId) || empty($this->mainLocationId)) {
            return false;
        }

        $response = $this->client->post(
            sprintf(self::LOCATION_POSTS_ENDPOINT, $this->accountId, $this->mainLocationId),
            ['body' => json_encode($values->toArray())]
        );

        $results = json_decode($response->getBody()->getContents(), true);
        if (Arr::has($results, 'error')) {
            $messages = array_filter(Arr::dot($results), function ($key) {
                return Str::endsWith($key, '.message');
            }, ARRAY_FILTER_USE_KEY);

            throw new Exception(implode('\n', $messages));
        }

        return $response->getStatusCode() === 200;
    }

    private function configure(): void
    {
        $this->client = new Client([
            'base_uri'          => self::BASE_API_URI,
            'debug'             => false,
            'http_errors'       => false,
            'headers'           => [
                'Accept'            => 'application/json',
                'Content-Type'      => 'application/json',
                'Accept-Language'   => 'fr;q=0.9, en;q=0.8, *;q=0.5',
                'Authorization'     => sprintf('Bearer %s', $this->accessToken ?? ''),
            ]
        ]);
    }
}