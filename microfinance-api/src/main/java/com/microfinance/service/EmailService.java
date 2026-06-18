package com.microfinance.service;

import lombok.Generated;
import org.slf4j.Logger;
import org.slf4j.LoggerFactory;
import org.springframework.beans.factory.annotation.Value;
import org.springframework.mail.SimpleMailMessage;
import org.springframework.mail.javamail.JavaMailSender;
import org.springframework.stereotype.Service;

@Service
public class EmailService {
   @Generated
   private static final Logger log = LoggerFactory.getLogger(EmailService.class);
   private final JavaMailSender mailSender;
   @Value("${spring.mail.username:noreply@zenpay.local}")
   private String expediteur;

   public EmailService(JavaMailSender mailSender) {
      this.mailSender = mailSender;
   }

   public void envoyerTokenConfirmation(String emailDestinataire, String token) {
      try {
         SimpleMailMessage message = new SimpleMailMessage();
         message.setFrom(this.expediteur);
         message.setTo(emailDestinataire);
         message.setSubject("Code de confirmation — Portail ZEN-PAY");
         message.setText("Bonjour,\n\nUne tentative de connexion a été détectée sur votre compte.\nVotre code de confirmation : " + token + "\n\nCe code est valable 10 minutes.\nSi vous n'êtes pas à l'origine de cette connexion, contactez le Super Administrateur.\n\nCordialement,\nZEN-PAY — Service Sécurité");
         this.mailSender.send(message);
         log.info("Token de confirmation envoyé à {}", emailDestinataire);
      } catch (Exception e) {
         log.warn("Impossible d'envoyer l'email à {} : {}", emailDestinataire, e.getMessage());
      }

   }

   public void envoyerCodeReset(String emailDestinataire, String code) {
      try {
         SimpleMailMessage message = new SimpleMailMessage();
         message.setFrom(this.expediteur);
         message.setTo(emailDestinataire);
         message.setSubject("Réinitialisation de mot de passe — ZEN-PAY");
         message.setText("Bonjour,\n\nVous avez demandé la réinitialisation de votre mot de passe ZEN-PAY.\nVotre code de réinitialisation : " + code + "\n\nCe code est valable 15 minutes.\nSi vous n'êtes pas à l'origine de cette demande, ignorez cet email.\n\nCordialement,\nZEN-PAY — Service Sécurité");
         this.mailSender.send(message);
         log.info("Code reset envoyé à {}", emailDestinataire);
      } catch (Exception e) {
         log.warn("Impossible d'envoyer le code reset à {} : {}", emailDestinataire, e.getMessage());
      }

   }

   public void envoyerNotificationTransaction(String email, String type, String montant, String reference) {
      try {
         SimpleMailMessage message = new SimpleMailMessage();
         message.setFrom(this.expediteur);
         message.setTo(email);
         message.setSubject("Confirmation de transaction — ZEN-PAY");
         message.setText("Bonjour,\n\nVotre transaction a été traitée avec succès.\nType : " + type + "\nMontant : " + montant + " FCFA\nRéférence : " + reference + "\n\nCordialement,\nZEN-PAY");
         this.mailSender.send(message);
      } catch (Exception e) {
         log.warn("Notification email non envoyée : {}", e.getMessage());
      }

   }
}
