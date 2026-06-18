package com.microfinance.service;

import com.microfinance.model.Rapport;
import com.microfinance.model.Transaction;
import com.microfinance.repository.CompteRepository;
import com.microfinance.repository.RapportRepository;
import com.microfinance.repository.TransactionRepository;
import java.math.BigDecimal;
import java.time.LocalDate;
import java.time.LocalDateTime;
import java.util.List;
import java.util.Map;
import java.util.stream.Collectors;
import lombok.Generated;
import org.springframework.stereotype.Service;
import org.springframework.transaction.annotation.Transactional;

@Service
@Transactional
public class RapportService {
   private final TransactionRepository transactionRepository;
   private final CompteRepository compteRepository;
   private final RapportRepository rapportRepository;

   public Rapport genererRapportPeriode(String type, LocalDate debut, LocalDate fin) {
      List<Transaction> transactions = this.transactionRepository.findByPeriode(debut.atStartOfDay(), fin.atTime(23, 59, 59));
      BigDecimal totalMontant = (BigDecimal)transactions.stream().map(Transaction::getMontant).reduce(BigDecimal.ZERO, BigDecimal::add);
      Map<String, Long> parType = (Map)transactions.stream().collect(Collectors.groupingBy((t) -> t.getClass().getSimpleName(), Collectors.counting()));
      String contenu = this.buildContenu(type, debut, fin, transactions.size(), totalMontant, parType);
      Rapport rapport = Rapport.builder().type(type).periodeDebut(debut).periodeFin(fin).contenu(contenu).build();
      return (Rapport)this.rapportRepository.save(rapport);
   }

   @Transactional(
      readOnly = true
   )
   public List listerRapports() {
      return this.rapportRepository.findAll();
   }

   private String buildContenu(String type, LocalDate debut, LocalDate fin, int nbTx, BigDecimal total, Map parType) {
      StringBuilder sb = new StringBuilder();
      sb.append("=== RAPPORT ").append(type).append(" ===\n");
      sb.append("Période : ").append(debut).append(" → ").append(fin).append("\n");
      sb.append("Généré le : ").append(LocalDateTime.now()).append("\n\n");
      sb.append("RÉSUMÉ :\n");
      sb.append("  Nombre de transactions : ").append(nbTx).append("\n");
      sb.append("  Montant total traité   : ").append(total).append(" FCFA\n\n");
      sb.append("DÉTAIL PAR TYPE :\n");
      parType.forEach((t, count) -> sb.append("  ").append(t).append(" : ").append(count).append("\n"));
      return sb.toString();
   }

   @Generated
   public RapportService(final TransactionRepository transactionRepository, final CompteRepository compteRepository, final RapportRepository rapportRepository) {
      this.transactionRepository = transactionRepository;
      this.compteRepository = compteRepository;
      this.rapportRepository = rapportRepository;
   }
}
