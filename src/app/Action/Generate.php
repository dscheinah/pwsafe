<?php
namespace App\Action;

use App\MiddlewareAbstract;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Sx\Message\Response\HelperInterface;

/**
 * Action to create a cryptographically secure password for the client.
 *
 * @package App\Action
 */
class Generate extends MiddlewareAbstract
{
    /**
     * The dictionary to be used by the generation.
     *
     * @var StreamInterface
     */
    private $dictionary;

    /**
     * Creates the action.
     *
     * @param HelperInterface $helper
     * @param StreamInterface $dictionary
     */
    public function __construct(HelperInterface $helper, StreamInterface $dictionary)
    {
        parent::__construct($helper);
        $this->dictionary = $dictionary;
    }

    /**
     * Creates a random password with printable characters according to the given settings form generation form.
     *
     * @param ServerRequestInterface  $request
     * @param RequestHandlerInterface $handler
     *
     * @return ResponseInterface
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        try {
            switch ($request->getAttribute('type')) {
                case 'random':
                    $password = $this->generateRandom(
                        max(min((int)$request->getAttribute('length', 0), 256), 0),
                        (array)$request->getAttribute('pattern', [])
                    );
                    break;
                case 'dictionary':
                    $password = $this->generateDictionary(
                        max(min((int)$request->getAttribute('words', 0), 16), 0),
                        (bool)$request->getAttribute('camel', false),
                        (bool)$request->getAttribute('space', false)
                    );
                    break;
                default:
                    return $this->helper->create(422, ['message' => 'Der ausgewählte Modus ist nicht verfügbar.']);
            }
            if (!$password) {
                return $this->helper->create(
                    422,
                    ['message' => 'Die aktuellen Einstellungen haben die Passworterstellung unterbunden.']
                );
            }
            return $this->helper->create(200, ['password' => $password]);
        } catch (\Exception $e) {
            return $this->helper->create(
                501,
                [
                    'message' => 'Bei der Zufallsgenerierung ist leider ein Fehler aufgetreten.',
                ]
            );
        }
    }

    /**
     * Generates a password with the given parameters from pure random.
     * Filter can be lower, upper, digits and symbols. All given values will be used for generation.
     *
     * @param int   $length
     * @param array $filter
     *
     * @return string
     * @throws \Exception
     */
    protected function generateRandom(int $length, array $filter): string
    {
        // Generate the regular expression from the given filters.
        $classes = [
            'lower' => '\x61-\x7A',
            'upper' => '\x41-\x5A',
            'digits' => '\x30-\x39',
            'symbols' => '\x21-\x7E',
        ];
        $selectedFilters = array_intersect_key($classes, array_flip($filter));
        // If no filters are selected, a password cannot be generated.
        if (!$selectedFilters) {
            return '';
        }
        $regex = sprintf('/[^%s]/', implode('', $selectedFilters));
        // Fill the password to the required length.
        // This needs to loop since char filters are applied to the random byte creation each run.
        $password = '';
        $current = 0;
        while ($current < $length) {
            // Create random bytes and filter it to printable chars.
            $password .= preg_replace($regex, '', random_bytes($length - $current));
            $current = \strlen($password);
        }
        return $password;
    }

    /**
     * Generates a password with the given parameters from a dictionary.
     *
     * @param int  $number
     * @param bool $camel
     * @param bool $space
     *
     * @return string
     * @throws \Exception
     */
    protected function generateDictionary(int $number, bool $camel, bool $space): string
    {
        // Remove empty lines around the file and convert to array to access each line.
        $dictionary = explode("\n", trim($this->dictionary));
        $max = \count($dictionary) - 1;
        $words = [];
        for ($i = 0; $i < $number; $i++) {
            $current = strtolower($dictionary[random_int(0, $max)]);
            if ($camel) {
                $current = ucfirst($current);
            }
            $words[] = $current;
        }
        return implode($space ? ' ' : '', $words);
    }
}
