<?php

namespace App\Http\Controllers;

use GuzzleHttp\Client;
use App\Models\Character;
use GuzzleHttp\Exception\GuzzleException;

/**
 * Controller to get all Characters from Marvel API
 * and store them in database
 */
class MarvelApiController extends Controller
{
    /**
     * PublicKey for API
     * @var string
     */
    private string $publicKey = '1c40d92b0b9936b84141c3de59cc2581';

    /**
     * PrivateKey for API
     * @var string
     */
    private string $privateKey = '5395eb5de34b69639553cb695e26080017dd33f3';

    /**
     * @return string
     * @throws GuzzleException
     */
    public function getDataFromMarvelApi(): string
    {
        $allCharacters = $this->getAllCharactersFromApi();

        foreach ($allCharacters as $result) {
            if ($result->id != null)
            {
                //Check if entry already exists in db
                $existingCharacter = Character::where('characterId', $result->id)->first();

                if (!$existingCharacter) {
                    $character = new Character([
                        'characterId' => $result->id,
                        'name' => $result->name,
                        'description' => $result->description,
                        'thumbnail' => $result->thumbnail->path .'.'. $result->thumbnail->extension,
                        'resource_uri' => $result->resourceURI,
                    ]);

                    $character->save();
                }
            }
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Character saved successfully'
        ]);
    }

    /**
     * @return array
     * @throws GuzzleException
     */
    private function getAllCharactersFromApi(): array
    {
        $allCharacters = [];
        $client = new Client();
        $limit = 100;
        $offset = 0;

        do {
            $response = $client->request('GET', 'https://gateway.marvel.com/v1/public/characters', [
                'query' => [
                    'apikey' => $this->publicKey,
                    'ts' => $this->getCurrentTimeStamp(),
                    'hash' => $this->generateHash(),
                    'limit' => $limit,
                    'offset' => $offset
                ]
            ]);

            $characterData = json_decode($response->getBody()->getContents());
            $allCharacters = array_merge($allCharacters, $characterData->data->results);
            $offset += $limit;
        } while ($offset <= $characterData->data->total);

        return $allCharacters;
    }

    /**
     * Returns the current TimeStamp
     * @return int
     */
    private function getCurrentTimeStamp(): int
    {
        return time();
    }

    /**
     * Generates a hash for the API call
     * @return string
     */
    private function generateHash(): string
    {
        return md5($this->getCurrentTimeStamp() . $this->privateKey . $this->publicKey);
    }
}
