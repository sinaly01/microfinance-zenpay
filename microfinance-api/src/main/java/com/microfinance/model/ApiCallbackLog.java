package com.microfinance.model;

import jakarta.persistence.Column;
import jakarta.persistence.Entity;
import jakarta.persistence.FetchType;
import jakarta.persistence.GeneratedValue;
import jakarta.persistence.GenerationType;
import jakarta.persistence.Id;
import jakarta.persistence.JoinColumn;
import jakarta.persistence.Lob;
import jakarta.persistence.ManyToOne;
import jakarta.persistence.PrePersist;
import jakarta.persistence.SequenceGenerator;
import jakarta.persistence.Table;
import java.time.LocalDateTime;
import lombok.Generated;

@Entity
@Table(
   name = "API_CALLBACK_LOGS"
)
public class ApiCallbackLog {
   @Id
   @GeneratedValue(
      strategy = GenerationType.SEQUENCE,
      generator = "callback_seq"
   )
   @SequenceGenerator(
      name = "callback_seq",
      sequenceName = "CALLBACK_SEQ",
      allocationSize = 1
   )
   @Column(
      name = "ID_CALLBACK"
   )
   private Long idCallback;
   @ManyToOne(
      fetch = FetchType.LAZY
   )
   @JoinColumn(
      name = "ID_TRANSACTION",
      nullable = false
   )
   private Transaction transaction;
   @Column(
      name = "OPERATEUR",
      length = 30
   )
   private String operateur;
   @Lob
   @Column(
      name = "CORPS_REPONSE_BRUTE",
      nullable = false
   )
   private String corpsReponseBrute;
   @Column(
      name = "CODE_STATUT_HTTP",
      nullable = false
   )
   private int codeStatutHttp;
   @Column(
      name = "DATE_RECEPTION",
      nullable = false
   )
   private LocalDateTime dateReception;

   @PrePersist
   public void prePersist() {
      this.dateReception = LocalDateTime.now();
   }

   @Generated
   public static ApiCallbackLogBuilder builder() {
      return new ApiCallbackLogBuilder();
   }

   @Generated
   public Long getIdCallback() {
      return this.idCallback;
   }

   @Generated
   public Transaction getTransaction() {
      return this.transaction;
   }

   @Generated
   public String getOperateur() {
      return this.operateur;
   }

   @Generated
   public String getCorpsReponseBrute() {
      return this.corpsReponseBrute;
   }

   @Generated
   public int getCodeStatutHttp() {
      return this.codeStatutHttp;
   }

   @Generated
   public LocalDateTime getDateReception() {
      return this.dateReception;
   }

   @Generated
   public void setIdCallback(final Long idCallback) {
      this.idCallback = idCallback;
   }

   @Generated
   public void setTransaction(final Transaction transaction) {
      this.transaction = transaction;
   }

   @Generated
   public void setOperateur(final String operateur) {
      this.operateur = operateur;
   }

   @Generated
   public void setCorpsReponseBrute(final String corpsReponseBrute) {
      this.corpsReponseBrute = corpsReponseBrute;
   }

   @Generated
   public void setCodeStatutHttp(final int codeStatutHttp) {
      this.codeStatutHttp = codeStatutHttp;
   }

   @Generated
   public void setDateReception(final LocalDateTime dateReception) {
      this.dateReception = dateReception;
   }

   @Generated
   public ApiCallbackLog() {
   }

   @Generated
   public ApiCallbackLog(final Long idCallback, final Transaction transaction, final String operateur, final String corpsReponseBrute, final int codeStatutHttp, final LocalDateTime dateReception) {
      this.idCallback = idCallback;
      this.transaction = transaction;
      this.operateur = operateur;
      this.corpsReponseBrute = corpsReponseBrute;
      this.codeStatutHttp = codeStatutHttp;
      this.dateReception = dateReception;
   }

   @Generated
   public static class ApiCallbackLogBuilder {
      @Generated
      private Long idCallback;
      @Generated
      private Transaction transaction;
      @Generated
      private String operateur;
      @Generated
      private String corpsReponseBrute;
      @Generated
      private int codeStatutHttp;
      @Generated
      private LocalDateTime dateReception;

      @Generated
      ApiCallbackLogBuilder() {
      }

      @Generated
      public ApiCallbackLogBuilder idCallback(final Long idCallback) {
         this.idCallback = idCallback;
         return this;
      }

      @Generated
      public ApiCallbackLogBuilder transaction(final Transaction transaction) {
         this.transaction = transaction;
         return this;
      }

      @Generated
      public ApiCallbackLogBuilder operateur(final String operateur) {
         this.operateur = operateur;
         return this;
      }

      @Generated
      public ApiCallbackLogBuilder corpsReponseBrute(final String corpsReponseBrute) {
         this.corpsReponseBrute = corpsReponseBrute;
         return this;
      }

      @Generated
      public ApiCallbackLogBuilder codeStatutHttp(final int codeStatutHttp) {
         this.codeStatutHttp = codeStatutHttp;
         return this;
      }

      @Generated
      public ApiCallbackLogBuilder dateReception(final LocalDateTime dateReception) {
         this.dateReception = dateReception;
         return this;
      }

      @Generated
      public ApiCallbackLog build() {
         return new ApiCallbackLog(this.idCallback, this.transaction, this.operateur, this.corpsReponseBrute, this.codeStatutHttp, this.dateReception);
      }

      @Generated
      public String toString() {
         Long var10000 = this.idCallback;
         return "ApiCallbackLog.ApiCallbackLogBuilder(idCallback=" + var10000 + ", transaction=" + String.valueOf(this.transaction) + ", operateur=" + this.operateur + ", corpsReponseBrute=" + this.corpsReponseBrute + ", codeStatutHttp=" + this.codeStatutHttp + ", dateReception=" + String.valueOf(this.dateReception) + ")";
      }
   }
}
