# Microsoft Graph Email (OAuth 2.0)

## Executive Summary
This document provides a production-ready OAuth 2.0 framework for Microsoft 365 email authentication and authorization. The integration enables secure, passwordless email sending via Microsoft Graph API without exposing user credentials.

## Architecture Overview

Authentication Flow:
Your Application -> Microsoft Entra ID -> Access Token -> Microsoft Graph API -> Send Email

## Key Components
- Microsoft Entra ID (Azure AD): Identity provider and OAuth 2.0 authorization server
- Microsoft Graph API: REST endpoint for email operations
- Client Credentials Flow: Service-to-service authentication for backend apps
- Application Permissions: Tenant-wide authorization without user interaction

## Credentials and Configuration

| Parameter | Value |
|-----------|-------|
| Tenant ID | `MS_GRAPH_TENANT_ID` |
| Client ID | `MS_GRAPH_CLIENT_ID` |
| Client Secret | `MS_GRAPH_CLIENT_SECRET` |
| Authority URL | `https://login.microsoftonline.com/{tenantId}` |
| Token Endpoint | `https://login.microsoftonline.com/{tenantId}/oauth2/v2.0/token` |
| API Endpoint | `https://graph.microsoft.com/v1.0/users/{mailbox}/sendMail` |
| Scope | `https://graph.microsoft.com/.default` |
| Grant Type | `client_credentials` |
| Mailbox | `MS_GRAPH_MAILBOX` |

### Required Permissions
- `Mail.Send` (Application Permission)
- Admin consent granted at the organizational level

### Permitted Mailbox
- Authorized mailbox: `no-reply@nijenhuis-botenverhuur.com`
- Set `MS_GRAPH_MAILBOX` to the permitted mailbox address

## Environment Variables
Set these in your `.env` (never commit secrets to version control):
```
MS_GRAPH_TENANT_ID=your_tenant_id_here
MS_GRAPH_CLIENT_ID=your_client_id_here
MS_GRAPH_CLIENT_SECRET=your_client_secret_here
MS_GRAPH_MAILBOX=no-reply@nijenhuis-botenverhuur.com
```

## Implementation Patterns

### Python (Backend)
```python
import os
import requests
from msal import ConfidentialClientApplication
from typing import Dict, Optional
import logging

logger = logging.getLogger(__name__)

class Office365MailService:
    """
    OAuth 2.0 wrapper for Microsoft Graph Mail API.
    """

    def __init__(self, client_id: str, client_secret: str, tenant_id: str, mailbox: str):
        self.client_id = client_id
        self.client_secret = client_secret
        self.tenant_id = tenant_id
        self.mailbox = mailbox
        self.authority = f"https://login.microsoftonline.com/{tenant_id}"
        self.scope = ["https://graph.microsoft.com/.default"]
        self.app = ConfidentialClientApplication(
            client_id=self.client_id,
            client_credential=self.client_secret,
            authority=self.authority,
        )

    def _get_access_token(self) -> str:
        token_response = self.app.acquire_token_silent(self.scope, account=None)
        if not token_response:
            token_response = self.app.acquire_token_for_client(scopes=self.scope)

        if "access_token" in token_response:
            return token_response["access_token"]

        error_msg = token_response.get("error_description", "Unknown error")
        logger.error(f"Token acquisition failed: {error_msg}")
        raise Exception(f"Failed to acquire access token: {error_msg}")

    def send_email(
        self,
        to_recipients: list,
        subject: str,
        body: str,
        body_type: str = "HTML",
        cc_recipients: Optional[list] = None,
        bcc_recipients: Optional[list] = None,
        save_to_sent: bool = True,
    ) -> Dict:
        access_token = self._get_access_token()

        recipients = [{"emailAddress": {"address": addr}} for addr in to_recipients]
        cc_list = [{"emailAddress": {"address": addr}} for addr in (cc_recipients or [])]
        bcc_list = [{"emailAddress": {"address": addr}} for addr in (bcc_recipients or [])]

        email_payload = {
            "message": {
                "subject": subject,
                "body": {"contentType": body_type, "content": body},
                "toRecipients": recipients,
                "ccRecipients": cc_list,
                "bccRecipients": bcc_list,
            },
            "saveToSentItems": save_to_sent,
        }

        headers = {
            "Authorization": f"Bearer {access_token}",
            "Content-Type": "application/json",
        }

        endpoint = f"https://graph.microsoft.com/v1.0/users/{self.mailbox}/sendMail"

        response = requests.post(endpoint, headers=headers, json=email_payload, timeout=30)
        response.raise_for_status()
        logger.info("Email sent successfully")
        return {"status": "success", "code": 202}

if __name__ == "__main__":
    service = Office365MailService(
        client_id=os.getenv("MS_GRAPH_CLIENT_ID"),
        client_secret=os.getenv("MS_GRAPH_CLIENT_SECRET"),
        tenant_id=os.getenv("MS_GRAPH_TENANT_ID"),
        mailbox=os.getenv("MS_GRAPH_MAILBOX", "no-reply@nijenhuis-botenverhuur.com"),
    )

    service.send_email(
        to_recipients=["customer@example.com"],
        subject="Your Booking Confirmation",
        body="<h2>Booking Confirmation</h2><p>Your booking has been confirmed.</p>",
        body_type="HTML",
    )
```

### Node.js/Express
```javascript
const { ClientSecretCredential } = require("@azure/identity");
const axios = require("axios");

class GraphMailClient {
  constructor(tenantId, clientId, clientSecret, mailbox) {
    this.tenantId = tenantId;
    this.clientId = clientId;
    this.clientSecret = clientSecret;
    this.mailbox = mailbox;
    this.credential = new ClientSecretCredential(tenantId, clientId, clientSecret);
  }

  async getAccessToken() {
    const token = await this.credential.getToken(
      "https://graph.microsoft.com/.default"
    );
    return token.token;
  }

  async sendEmail(options) {
    const { to, subject, body, bodyType = "HTML", cc = [], bcc = [] } = options;
    const accessToken = await this.getAccessToken();

    const payload = {
      message: {
        subject,
        body: { contentType: bodyType, content: body },
        toRecipients: to.map((addr) => ({ emailAddress: { address: addr } })),
        ccRecipients: cc.map((addr) => ({ emailAddress: { address: addr } })),
        bccRecipients: bcc.map((addr) => ({ emailAddress: { address: addr } })),
      },
      saveToSentItems: true,
    };

    await axios.post(
      `https://graph.microsoft.com/v1.0/users/${this.mailbox}/sendMail`,
      payload,
      {
        headers: {
          Authorization: `Bearer ${accessToken}`,
          "Content-Type": "application/json",
        },
      }
    );
  }
}

const express = require("express");
const router = express.Router();

const mailClient = new GraphMailClient(
  process.env.MS_GRAPH_TENANT_ID,
  process.env.MS_GRAPH_CLIENT_ID,
  process.env.MS_GRAPH_CLIENT_SECRET,
  process.env.MS_GRAPH_MAILBOX || "no-reply@nijenhuis-botenverhuur.com"
);

router.post("/booking/confirm", async (req, res) => {
  try {
    const { customerEmail, bookingId, bookingDate } = req.body;
    await mailClient.sendEmail({
      to: [customerEmail],
      subject: "Your Booking Confirmation",
      body: `<h2>Booking Confirmed</h2><p>Booking ID: ${bookingId}</p><p>Date: ${bookingDate}</p>`,
      bodyType: "HTML",
    });
    res.json({ success: true, message: "Confirmation email sent" });
  } catch (error) {
    res.status(500).json({ success: false, error: error.message });
  }
});

module.exports = router;
```

### PHP
```php
<?php

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class MicrosoftGraphMailService
{
    private $clientId;
    private $clientSecret;
    private $tenantId;
    private $mailbox;
    private $httpClient;

    public function __construct($clientId, $clientSecret, $tenantId, $mailbox)
    {
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
        $this->tenantId = $tenantId;
        $this->mailbox = $mailbox;
        $this->httpClient = new Client();
    }

    public function getAccessToken()
    {
        try {
            $response = $this->httpClient->post(
                "https://login.microsoftonline.com/{$this->tenantId}/oauth2/v2.0/token",
                [
                    'form_params' => [
                        'client_id' => $this->clientId,
                        'client_secret' => $this->clientSecret,
                        'scope' => 'https://graph.microsoft.com/.default',
                        'grant_type' => 'client_credentials'
                    ]
                ]
            );

            $data = json_decode($response->getBody(), true);
            return $data['access_token'];
        } catch (RequestException $e) {
            error_log("Token acquisition failed: " . $e->getMessage());
            throw new Exception("Failed to acquire access token");
        }
    }

    public function sendEmail($to, $subject, $body, $options = [])
    {
        $bodyType = $options['bodyType'] ?? 'HTML';
        $cc = $options['cc'] ?? [];
        $bcc = $options['bcc'] ?? [];
        $accessToken = $this->getAccessToken();

        $payload = [
            'message' => [
                'subject' => $subject,
                'body' => [
                    'contentType' => $bodyType,
                    'content' => $body
                ],
                'toRecipients' => array_map(fn($addr) => [
                    'emailAddress' => ['address' => $addr]
                ], (array)$to),
                'ccRecipients' => array_map(fn($addr) => [
                    'emailAddress' => ['address' => $addr]
                ], $cc),
                'bccRecipients' => array_map(fn($addr) => [
                    'emailAddress' => ['address' => $addr]
                ], $bcc)
            ],
            'saveToSentItems' => true
        ];

        try {
            $this->httpClient->post(
                "https://graph.microsoft.com/v1.0/users/{$this->mailbox}/sendMail",
                [
                    'headers' => [
                        'Authorization' => "Bearer {$accessToken}",
                        'Content-Type' => 'application/json'
                    ],
                    'json' => $payload
                ]
            );
            return ['status' => 'success', 'code' => 202];
        } catch (RequestException $e) {
            error_log("Email send failed: " . $e->getResponse()->getBody());
            throw new Exception("Graph API error: " . $e->getMessage());
        }
    }
}

$mailService = new MicrosoftGraphMailService(
    getenv('MS_GRAPH_CLIENT_ID'),
    getenv('MS_GRAPH_CLIENT_SECRET'),
    getenv('MS_GRAPH_TENANT_ID'),
    getenv('MS_GRAPH_MAILBOX') ?: 'no-reply@nijenhuis-botenverhuur.com'
);

try {
    $mailService->sendEmail('customer@example.com', 'Booking Confirmation', '<h2>Your booking is confirmed</h2>');
    echo "Email sent successfully";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
```

## Security Best Practices
1. Credential Management
   - Store credentials in environment variables only
   - Use `.env` files locally, never commit secrets
   - Rotate client secrets regularly (for example, every 90 days)
   - Automate secret rotation in CI/CD pipelines
2. Access Control
   - Follow least privilege and keep only `Mail.Send` as application permission
   - Use an Exchange Online application access policy to restrict allowed mailboxes
   - Log and monitor Graph API usage for unusual activity

## Local Development: Booking Confirmation Email

### Required `.env` Values
Set these locally to match the Azure app registration:
```
APP_ENV=development
MS_GRAPH_TENANT_ID=your_tenant_id_here
MS_GRAPH_CLIENT_ID=your_client_id_here
MS_GRAPH_CLIENT_SECRET=your_client_secret_here
MS_GRAPH_MAILBOX=no-reply@nijenhuis-botenverhuur.com
```

### Manual Paid Webhook Trigger (Local Only)
When running locally, Mollie cannot reach localhost, so use the manual trigger endpoint:
```
GET /webhook/mollie?action=simulatePaid&paymentId=PAYMENT_ID
```
or
```
GET /webhook/mollie?action=simulatePaid&bookingId=BOOKING_ID
```

This simulates a paid webhook, updates the booking status, and sends the confirmation email through Microsoft Graph.
