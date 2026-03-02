import api from './api'

export const loanService = {
  // Analyze a new loan application
  analyzeLoan(applicationData) {
    return api.post('/loans/analyze', applicationData)
  },

  // Get all applications
  getApplications(filters = {}) {
    return api.get('/loans/applications', {
      params: filters
    })
  },

  // Get a single application by ID
  getApplication(applicationId) {
    return api.get(`/loans/applications/${applicationId}`)
  },

  // Get applications for a specific client
  getClientApplications(clientId) {
    return api.get(`/loans/clients/${clientId}/applications`)
  },

  // Get statistics
  getStatistics() {
    return api.get('/loans/statistics')
  },

  // Poll for application analysis result
  async pollApplicationStatus(applicationId, maxAttempts = 30, interval = 1000) {
    for (let attempt = 0; attempt < maxAttempts; attempt++) {
      try {
        const response = await this.getApplication(applicationId)
        const application = response.data.data

        // If analysis is done (not pending), return the result
        if (application.status !== 'pending_analysis') {
          return application
        }

        // Wait before polling again
        await new Promise(resolve => setTimeout(resolve, interval))
      } catch (error) {
        console.error('Error polling application status:', error)
        throw error
      }
    }

    // Timeout reached
    throw new Error('Application analysis timeout')
  }
}
