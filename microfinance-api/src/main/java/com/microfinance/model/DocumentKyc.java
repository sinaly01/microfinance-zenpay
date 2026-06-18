package com.microfinance.model;

import jakarta.persistence.Column;
import jakarta.persistence.Entity;
import jakarta.persistence.FetchType;
import jakarta.persistence.GeneratedValue;
import jakarta.persistence.GenerationType;
import jakarta.persistence.Id;
import jakarta.persistence.JoinColumn;
import jakarta.persistence.ManyToOne;
import jakarta.persistence.PrePersist;
import jakarta.persistence.SequenceGenerator;
import jakarta.persistence.Table;
import java.time.LocalDateTime;
import lombok.Generated;

@Entity
@Table(
   name = "CLIENT_DOCUMENTS_KYC"
)
public class DocumentKyc {
   @Id
   @GeneratedValue(
      strategy = GenerationType.SEQUENCE,
      generator = "document_seq"
   )
   @SequenceGenerator(
      name = "document_seq",
      sequenceName = "DOCUMENT_SEQ",
      allocationSize = 1
   )
   @Column(
      name = "ID_DOCUMENT"
   )
   private Long idDocument;
   @ManyToOne(
      fetch = FetchType.LAZY
   )
   @JoinColumn(
      name = "ID_CLIENT",
      nullable = false
   )
   private Client client;
   @Column(
      name = "TYPE_DOCUMENT",
      nullable = false,
      length = 50
   )
   private String typeDocument;
   @Column(
      name = "URL_DOCUMENT",
      nullable = false,
      length = 500
   )
   private String urlDocument;
   @Column(
      name = "DATE_TELEVERSEMENT",
      nullable = false
   )
   private LocalDateTime dateTeleversement;

   @PrePersist
   public void prePersist() {
      this.dateTeleversement = LocalDateTime.now();
   }

   @Generated
   public static DocumentKycBuilder builder() {
      return new DocumentKycBuilder();
   }

   @Generated
   public Long getIdDocument() {
      return this.idDocument;
   }

   @Generated
   public Client getClient() {
      return this.client;
   }

   @Generated
   public String getTypeDocument() {
      return this.typeDocument;
   }

   @Generated
   public String getUrlDocument() {
      return this.urlDocument;
   }

   @Generated
   public LocalDateTime getDateTeleversement() {
      return this.dateTeleversement;
   }

   @Generated
   public void setIdDocument(final Long idDocument) {
      this.idDocument = idDocument;
   }

   @Generated
   public void setClient(final Client client) {
      this.client = client;
   }

   @Generated
   public void setTypeDocument(final String typeDocument) {
      this.typeDocument = typeDocument;
   }

   @Generated
   public void setUrlDocument(final String urlDocument) {
      this.urlDocument = urlDocument;
   }

   @Generated
   public void setDateTeleversement(final LocalDateTime dateTeleversement) {
      this.dateTeleversement = dateTeleversement;
   }

   @Generated
   public DocumentKyc() {
   }

   @Generated
   public DocumentKyc(final Long idDocument, final Client client, final String typeDocument, final String urlDocument, final LocalDateTime dateTeleversement) {
      this.idDocument = idDocument;
      this.client = client;
      this.typeDocument = typeDocument;
      this.urlDocument = urlDocument;
      this.dateTeleversement = dateTeleversement;
   }

   @Generated
   public static class DocumentKycBuilder {
      @Generated
      private Long idDocument;
      @Generated
      private Client client;
      @Generated
      private String typeDocument;
      @Generated
      private String urlDocument;
      @Generated
      private LocalDateTime dateTeleversement;

      @Generated
      DocumentKycBuilder() {
      }

      @Generated
      public DocumentKycBuilder idDocument(final Long idDocument) {
         this.idDocument = idDocument;
         return this;
      }

      @Generated
      public DocumentKycBuilder client(final Client client) {
         this.client = client;
         return this;
      }

      @Generated
      public DocumentKycBuilder typeDocument(final String typeDocument) {
         this.typeDocument = typeDocument;
         return this;
      }

      @Generated
      public DocumentKycBuilder urlDocument(final String urlDocument) {
         this.urlDocument = urlDocument;
         return this;
      }

      @Generated
      public DocumentKycBuilder dateTeleversement(final LocalDateTime dateTeleversement) {
         this.dateTeleversement = dateTeleversement;
         return this;
      }

      @Generated
      public DocumentKyc build() {
         return new DocumentKyc(this.idDocument, this.client, this.typeDocument, this.urlDocument, this.dateTeleversement);
      }

      @Generated
      public String toString() {
         Long var10000 = this.idDocument;
         return "DocumentKyc.DocumentKycBuilder(idDocument=" + var10000 + ", client=" + String.valueOf(this.client) + ", typeDocument=" + this.typeDocument + ", urlDocument=" + this.urlDocument + ", dateTeleversement=" + String.valueOf(this.dateTeleversement) + ")";
      }
   }
}
