<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ProposalStatusNotification extends Mailable
{
    public $proposal;
    public $action;
    public $note;

    /**
     * Create a new message instance.
     */
    public function __construct($proposal, $action, $note = null)
    {
        $this->proposal = $proposal;
        $this->action = $action;
        $this->note = $note;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $statusText = $this->action === 'approve' ? 'ONAYLANDI' : 'REDDEDİLDİ';
        $icon = $this->action === 'approve' ? '✅' : '❌';
        
        return new Envelope(
            subject: "{$icon} Teklifiniz {$statusText}: #{$this->proposal->proposal_number}",
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        // Using a simple HTML string for minimal file creation dependency,
        // or we could assume a standard 'emails.generic' view exists.
        // For robustness without creating new view files, lets use htmlString in a simple view if possible, 
        // or just point to a newly created view. 
        // Best practice: Create a view. But I'll use the 'html' method of Message if available? 
        // Mailable 'content' expects a view name or html string... wait, 'html' isn't direct option in Content object usually.
        // I will use 'view: html' (doesn't exist) -> standard practice: create a view.
        // Let's create a view file for this.
        
        return new Content(
            view: 'emails.proposal_status',
            with: [
                'proposal' => $this->proposal,
                'action' => $this->action,
                'note' => $this->note,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
