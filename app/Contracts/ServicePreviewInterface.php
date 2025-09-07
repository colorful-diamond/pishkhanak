<?php

namespace App\Contracts;

use App\Models\Service;

interface ServicePreviewInterface
{
    /**
     * Get preview data for the service
     * This method should make API calls and return engaging preview information
     *
     * @param array $serviceData The submitted service data
     * @param Service $service The service model
     * @return array Preview data array with keys:
     *               - 'success' => bool
     *               - 'preview_data' => array (preview information to show)
     *               - 'engagement_message' => string (message to encourage payment)
     */
    public function getPreviewData(array $serviceData, Service $service): array;

    /**
     * Check if this service supports preview
     *
     * @return bool
     */
    public function supportsPreview(): bool;

    /**
     * Get the preview template name for this service
     * This allows different services to have different preview layouts
     *
     * @return string
     */
    public function getPreviewTemplate(): string;
} 