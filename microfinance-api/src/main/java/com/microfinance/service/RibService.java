package com.microfinance.service;

import com.lowagie.text.Document;
import com.lowagie.text.Font;
import com.lowagie.text.PageSize;
import com.lowagie.text.Paragraph;
import com.lowagie.text.Phrase;
import com.lowagie.text.pdf.PdfPCell;
import com.lowagie.text.pdf.PdfPTable;
import com.lowagie.text.pdf.PdfWriter;
import com.microfinance.exception.BusinessException;
import com.microfinance.exception.ResourceNotFoundException;
import com.microfinance.model.Client;
import com.microfinance.model.Compte;
import com.microfinance.repository.ClientRepository;
import com.microfinance.repository.CompteRepository;
import java.awt.Color;
import java.io.ByteArrayOutputStream;
import java.time.LocalDateTime;
import java.time.format.DateTimeFormatter;
import java.util.List;
import lombok.Generated;
import org.springframework.stereotype.Service;

@Service
public class RibService {
   private final ClientRepository clientRepository;
   private final CompteRepository compteRepository;

   public byte[] genererRib(Long idClient) {
      Client client = (Client)this.clientRepository.findById(idClient).orElseThrow(() -> new ResourceNotFoundException("Client", idClient));
      if (client.getOffreAbonnement() != null && client.getOffreAbonnement().isOptionRibDispo()) {
         List<Compte> comptes = this.compteRepository.findAll().stream().filter((c) -> c.getClient().getIdClient().equals(idClient) && c.isActif()).toList();
         if (comptes.isEmpty()) {
            throw new BusinessException("Aucun compte actif trouvé pour ce client.");
         } else {
            return this.construirePdf(client, (Compte)comptes.get(0));
         }
      } else {
         throw new BusinessException("La génération de RIB est réservée aux clients ayant souscrit à l'Offre 2.");
      }
   }

   private byte[] construirePdf(Client client, Compte compte) {
      try {
         ByteArrayOutputStream baos = new ByteArrayOutputStream();

         byte[] var13;
         try {
            Document document = new Document(PageSize.A4);
            PdfWriter.getInstance(document, baos);
            document.open();
            Font titreFont = new Font(1, 18.0F, 1, new Color(0, 102, 204));
            Font labelFont = new Font(1, 10.0F, 1);
            Font valueFont = new Font(1, 10.0F, 0);
            Font smallFont = new Font(1, 8.0F, 2, Color.GRAY);
            Paragraph titre = new Paragraph("ZEN-PAY MICROFINANCE", titreFont);
            titre.setAlignment(1);
            document.add(titre);
            Paragraph sousTitre = new Paragraph("Relevé d'Identité Bancaire (RIB)", new Font(1, 14.0F, 1));
            sousTitre.setAlignment(1);
            sousTitre.setSpacingBefore(5.0F);
            document.add(sousTitre);
            document.add(new Paragraph("\n"));
            PdfPTable table = new PdfPTable(2);
            table.setWidthPercentage(100.0F);
            table.setSpacingBefore(10.0F);
            String var10003 = client.getNom();
            this.addRow(table, "Titulaire du compte", var10003 + " " + client.getPrenom(), labelFont, valueFont);
            this.addRow(table, "Numéro de compte (BCEAO)", compte.getNumeroCompte(), labelFont, valueFont);
            this.addRow(table, "Type de compte", compte.getStatut().name(), labelFont, valueFont);
            this.addRow(table, "Téléphone MoMo", client.getTelephone(), labelFont, valueFont);
            this.addRow(table, "Opérateur Mobile Money", client.getOperateurMomo() != null ? client.getOperateurMomo() : "N/A", labelFont, valueFont);
            this.addRow(table, "Offre souscrite", client.getOffreAbonnement().getNomOffre(), labelFont, valueFont);
            this.addRow(table, "Date d'ouverture", compte.getDateOuverture().format(DateTimeFormatter.ofPattern("dd/MM/yyyy")), labelFont, valueFont);
            this.addRow(table, "Banque / Institution", "ZEN-PAY Microfinance — Zone UMOA/BCEAO", labelFont, valueFont);
            this.addRow(table, "Code banque", "99999", labelFont, valueFont);
            this.addRow(table, "Code pays (ISO)", "CI", labelFont, valueFont);
            this.addRow(table, "Devise", "XOF (Franc CFA BCEAO)", labelFont, valueFont);
            document.add(table);
            document.add(new Paragraph("\n"));
            Paragraph pied = new Paragraph("Document généré le " + LocalDateTime.now().format(DateTimeFormatter.ofPattern("dd/MM/yyyy à HH:mm")) + " — ZEN-PAY Microfinance. Ce document est confidentiel.", smallFont);
            pied.setAlignment(1);
            document.add(pied);
            document.close();
            var13 = baos.toByteArray();
         } catch (Throwable var15) {
            try {
               baos.close();
            } catch (Throwable var14) {
               var15.addSuppressed(var14);
            }

            throw var15;
         }

         baos.close();
         return var13;
      } catch (Exception e) {
         throw new BusinessException("Erreur lors de la génération du RIB PDF : " + e.getMessage());
      }
   }

   private void addRow(PdfPTable table, String label, String value, Font labelFont, Font valueFont) {
      PdfPCell labelCell = new PdfPCell(new Phrase(label, labelFont));
      labelCell.setBackgroundColor(new Color(240, 248, 255));
      labelCell.setPadding(6.0F);
      table.addCell(labelCell);
      PdfPCell valueCell = new PdfPCell(new Phrase(value, valueFont));
      valueCell.setPadding(6.0F);
      table.addCell(valueCell);
   }

   @Generated
   public RibService(final ClientRepository clientRepository, final CompteRepository compteRepository) {
      this.clientRepository = clientRepository;
      this.compteRepository = compteRepository;
   }
}
