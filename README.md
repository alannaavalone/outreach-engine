# Outreach Engine
Automated outreach system using Twilio, Discord, Nutshell, and Bluehost

---

## Overview
The Outreach Engine is an automated system designed to manage outbound and inbound communication with real estate investors.

It connects Twilio, Nutshell CRM, Discord, email workflows, and a PHP backend hosted on Bluehost to create a fully integrated outreach system.

---

## Problem
Lead follow-up was inconsistent, manual, and difficult to track.

There was no centralized system to handle:
- calls  
- messages  
- emails  
- responses  
- activity logging  

---

## Solution
Built an automated system that:

- Initiates outbound contact using voicemail drops
- Sends SMS follow-ups automatically
- Sends follow-up emails based on campaign logic
- Logs all activity inside Nutshell CRM
- Handles inbound replies from borrowers
- Routes inbound messages into Discord as a real-time inbox
- Allows replying directly from Discord back to the borrower via Twilio
- Runs backend logic using PHP hosted on Bluehost

---

## System Flow
Lead → Twilio Call → Voicemail Drop
→ SMS Follow-up
→ Email Follow-up
→ Nutshell Logging
→ Inbound Reply Handling
→ Discord Inbox (Receive + Reply)
→ PHP Backend (Bluehost)

---

## Tech Stack

- Twilio (Calls, SMS, Webhooks)
- Nutshell CRM (Activity Logging via API)
- Discord (Two-way communication layer: inbox + reply interface)
- Email Delivery (SMTP / automation layer)
- PHP (Backend Logic)
- Bluehost (Hosting Environment)

---

## Key Architecture Decisions

- Separated outbound and inbound logic for clarity and scalability
- Used Twilio Functions for real-time communication handling
- Centralized all activity logging inside Nutshell CRM
- Designed Discord as a two-way communication layer (inbox + reply system)
- Integrated email as an additional follow-up channel
- Used PHP on Bluehost to handle backend processing and integrations

---

## Modules

### Outbound Engine
- Voicemail drops
- SMS follow-up sequences
- Email follow-up triggers

### Inbound Processing
- Auto-reply logic
- Message handling
- CRM activity logging

### Email Layer
- Follow-up email delivery
- Campaign-based email touchpoints
- Email activity logging

### Discord Communication Layer
- Receives inbound borrower messages in real-time
- Acts as a centralized inbox for SMS replies
- Allows replying directly from Discord back to the borrower
- Provides instant visibility and mobile notifications

### Backend Layer
- PHP scripts handling integrations and logic
- Hosted on Bluehost

---

## Reactivation Engine

The Reactivation Engine is a sub-system designed to re-engage inactive or unresponsive leads through structured follow-up campaigns.

### Campaign Structure

- Kickoff Campaign (Monday)
  - Voicemail drop
  - SMS follow-up
  - Email touchpoint

- Follow-Up Campaign (Wednesday)
  - Second message touchpoint
  - Email follow-up
  - Re-engagement attempt

---

### System Flow
Inactive Lead → Kickoff (Call + SMS + Email)
→ Wait Period
→ Follow-Up (SMS + Email)
→ Response Handling
→ CRM Logging
→ Discord Inbox (Receive + Reply)

---

### Key Logic

- Uses separate launcher and worker functions
- Tracks responses and engagement status
- Avoids duplicate messaging
- Logs all activity inside Nutshell CRM
- Supports two-way communication via Discord interface

---

### Outcome

- Increased response rate from old leads
- Structured follow-up system
- Multi-channel engagement strategy
- Reduced manual outreach

---

## Outcome

- Faster lead engagement
- No missed follow-ups
- Centralized communication tracking
- Multi-channel outreach system (call, SMS, email)
- Real-time communication via Discord inbox
- Scalable outreach infrastructure

---

## Notes

This system is continuously evolving and will expand to include:
- lead intake processing  
- AI-driven communication
