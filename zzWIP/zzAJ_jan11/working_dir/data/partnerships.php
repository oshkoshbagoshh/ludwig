<?php
function getPartnerships()
{
    // This could come from your database in a real application
    $partners = [
        [
            'id' => 1,
            'name' => 'Le Moyne Consulting',
            'description' => 'Providing expert technical resources and solutions.',
            'logo' => '/images/partners/lemoyne-logo.png', // Add actual logo path
            'website' => 'https://lemoyneconsulting.com',
        ],
        [
            'id' => 2,
            'name' => 'ComplySAM',
            'description' => 'Secure and compliant data handling solutions.',
            'logo' => '/images/partners/complysam-logo.png', // Add actual logo path
            'website' => 'https://complysam.com',
        ],
        [
            'id' => 3,
            'name' => 'Pentacost.ai',
            'description' => 'Ethical Web Development Services and Process Management Consulting',
            'logo' => '/images/partners/pentacost.ai.png', // Add actual logo path
            'website' => 'https://pentacost.ai',
        ],
        [
            'id' => 4,
            'name' => 'Damme Good Recordings',
            'description' => 'Sam Van Damme Recording Studio, featuring AJ',
            'logo' => '/images/partners/damgoode/logo.png', // Add actual logo path
            'website' => '#',
        ],
    ];

    return $partners;

}
