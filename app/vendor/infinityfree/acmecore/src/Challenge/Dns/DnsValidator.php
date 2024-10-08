<?php

/*
 * This file is part of the Acme PHP project.
 *
 * (c) Titouan Galopin <galopintitouan@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace InfinityFree\AcmeCore\Challenge\Dns;

use InfinityFree\AcmeCore\Challenge\SolverInterface;
use InfinityFree\AcmeCore\Challenge\ValidatorInterface;
use InfinityFree\AcmeCore\Exception\AcmeDnsResolutionException;
use InfinityFree\AcmeCore\Protocol\AuthorizationChallenge;

/**
 * Validator for DNS challenges.
 *
 * @author Jérémy Derussé <jeremy@derusse.com>
 */
class DnsValidator implements ValidatorInterface
{
    /**
     * @var DnsDataExtractor
     */
    private $extractor;

    /**
     * @var DnsResolverInterface
     */
    private $dnsResolver;

    public function __construct(DnsDataExtractor $extractor = null, DnsResolverInterface $dnsResolver = null)
    {
        $this->extractor = $extractor ?: new DnsDataExtractor();

        $this->dnsResolver = $dnsResolver;
        if (!$this->dnsResolver) {
            $this->dnsResolver = LibDnsResolver::isSupported() ? new LibDnsResolver() : new SimpleDnsResolver();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function supports(AuthorizationChallenge $authorizationChallenge, SolverInterface $solver): bool
    {
        return 'dns-01' === $authorizationChallenge->getType();
    }

    /**
     * {@inheritdoc}
     */
    public function isValid(AuthorizationChallenge $authorizationChallenge, SolverInterface $solver): bool
    {
        $recordName = $this->extractor->getRecordName($authorizationChallenge);
        $recordValue = $this->extractor->getRecordValue($authorizationChallenge);

        try {
            return \in_array($recordValue, $this->dnsResolver->getTxtEntries($recordName), false);
        } catch (AcmeDnsResolutionException $e) {
            return false;
        }
    }
}
