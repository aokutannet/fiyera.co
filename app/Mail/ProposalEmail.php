<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ProposalEmail extends Mailable
{
    public $proposal;

    /**
     * Create a new message instance.
     */
    public function __construct($proposal)
    {
        $this->proposal = $proposal;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $companyName = $this->proposal->user?->tenant?->name ?? config('app.name');
        return new Envelope(
            subject: "[{$companyName}] Tarafından Oluşturulmuş bir Teklifiniz Var!",
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.proposal',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        $proposal = $this->proposal;
        
        // Fetch layout settings (reusing logic for consistency)
        $settings = \App\Models\Setting::whereIn('key', [
            'proposal_layout', 
            'proposal_color_primary', 
            'proposal_color_secondary'
        ])->get()->keyBy('key');

        $layout = json_decode($settings['proposal_layout']->value ?? '[]', true);
        if (empty($layout)) {
            $layout = [
                ['id' => 'header', 'visible' => true],
                ['id' => 'separator_1', 'visible' => true],
                ['id' => 'recipient', 'visible' => true],
                ['id' => 'items', 'visible' => true],
                ['id' => 'summary', 'visible' => true],
                ['id' => 'notes', 'visible' => true],
                ['id' => 'footer', 'visible' => true],
            ];
        }

        $primaryColor = $settings['proposal_color_primary']->value ?? '#111827';
        $secondaryColor = $settings['proposal_color_secondary']->value ?? '#6B7280';

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('tenant.proposals.print', compact('proposal', 'layout', 'primaryColor', 'secondaryColor') + ['isPdf' => true]);

        return [
            \Illuminate\Mail\Mailables\Attachment::fromData(
                fn () => $pdf->output(),
                $proposal->proposal_number . '.pdf'
            )->withMime('application/pdf'),
        ];
    }
}
