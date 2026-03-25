# outreach-engine
Automated outreach system using Twilio, Discord, Nutshell, and Bluehost

## Overview
The Outreach Engine is an automated system designed to manage outbound and inbound communication with real estate investors. It connects Twilio, Nutshell CRM, Discord, and a PHP backend hosted on Bluehost to create a fully integrated outreach workflow.

## Problem
Lead follow-up was inconsistent, manual, and difficult to track. There was no centralized system to handle calls, messages, responses, and activity logging.

## Solution
Built an automated system that:

- Initiates outbound contact using voicemail drops
- Sends SMS follow-ups automatically
- Logs all activity inside Nutshell CRM
- Handles inbound replies from borrowers
- Sends real-time alerts via Discord
- Runs backend logic using PHP hosted on Bluehost

## System Flow
Lead → Twilio Call → Voicemail Drop
→ SMS Follow-up
→ Nutshell Logging
→ Inbound Reply Handling
→ Discord Alert
→ PHP Backend (Bluehost)

---

## Tech Stack

- Twilio (Calls, SMS, Webhooks)
- Nutshell CRM (Activity Logging via API)
- Discord (Notification Layer)
- PHP (Backend Logic)
- Bluehost (Hosting Environment)

---

## Key Architecture Decisions

- Separated outbound and inbound logic for clarity and scalability
- Used Twilio Functions for real-time communication handling
- Centralized all activity logging inside Nutshell CRM
- Added Discord as a visibility layer for inbound messages
- Used PHP on Bluehost to handle backend processing and integrations

---

## Modules

### Outbound Engine
- Voicemail drops
- SMS follow-up sequences

### Inbound Processing
- Auto-reply logic
- Message handling
- CRM activity logging

### Notification Layer
- Discord alerts for incoming messages

### Backend Layer
- PHP scripts handling integrations and logic
- Hosted on Bluehost

## Outcome

- Faster lead engagement
- No missed follow-ups
- Centralized communication tracking
- Scalable outreach infrastructure

## Notes

This system is continuously evolving and will expand to include additional automation layers such as lead intake processing and AI-driven communication.
